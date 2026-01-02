// Chat Notification System - Only for Admin
class ChatNotificationManager {
    constructor() {
        this.container = null;
        this.notifications = new Map();
        this.soundEnabled = true;
        this.audioUnlocked = false;
        this.preloadedAudio = null;
        this.echoChannel = null;
        this.pushNotificationEnabled = false;
        this.init();
        this.unlockAudio();
        this.requestNotificationPermission();
    }

    requestNotificationPermission() {
        if (!('Notification' in window)) {
            return;
        }

        if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.pushNotificationEnabled = true;
                    this.showTestNotification();
                }
            });
        } else if (Notification.permission === 'granted') {
            this.pushNotificationEnabled = true;
        }
    }

    showTestNotification() {
        const notification = new Notification('Chat Notification Aktif', {
            body: 'Anda akan menerima notifikasi chat di sini',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'test-notification',
            requireInteraction: false,
            silent: false
        });

        setTimeout(() => notification.close(), 3000);
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initContainer());
        } else {
            this.initContainer();
        }
    }

    unlockAudio() {
        this.preloadedAudio = new Audio('/sounds/42289.mp3');
        this.preloadedAudio.volume = 0.5;
        this.preloadedAudio.load();

        const unlockAudioContext = () => {
            if (this.audioUnlocked) return;

            this.preloadedAudio.play()
                .then(() => {
                    this.preloadedAudio.pause();
                    this.preloadedAudio.currentTime = 0;
                    this.audioUnlocked = true;
                })
                .catch(() => {});
        };

        const events = ['click', 'touchstart', 'keydown', 'scroll', 'mousemove'];
        events.forEach(eventType => {
            document.addEventListener(eventType, unlockAudioContext, { once: true, passive: true });
        });
    }

    initContainer() {
        this.container = document.createElement('div');
        this.container.className = 'chat-notification-container';
        document.body.appendChild(this.container);

        this.setupEchoListener();
    }

    setupEchoListener() {
        if (!window.Echo) {
            setTimeout(() => this.setupEchoListener(), 100);
            return;
        }

        const userId = window.userId;
        if (!userId) {
            return;
        }

        this.echoChannel = window.Echo.private(`chat.${userId}`)
            .subscribed(() => {})
            .listenToAll((event, data) => {})
            .listen('.MessageSent', (e) => {
                const onChatPage = window.location.pathname.includes('/chat');
                const viewingThisUser = window.selectedUserId && window.selectedUserId == e.sender_id;

                if (!onChatPage || !viewingThisUser) {
                    const senderName = e.sender ? e.sender.name : 'User';
                    this.show({
                        id: e.id,
                        sender: senderName,
                        message: e.message,
                        time: new Date(e.created_at).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }),
                        senderId: e.sender_id
                    });
                }
            })
            .error((error) => {});
    }

    show(data) {
        const { id, sender, message, time, senderId } = data;

        if (this.notifications.has(id)) {
            return;
        }

        this.showPushNotification(sender, message, senderId);

        const notification = document.createElement('div');
        notification.className = 'chat-notification';
        notification.dataset.id = id;
        notification.dataset.senderId = senderId;

        const initials = this.getInitials(sender);
        const displayTime = time || new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        notification.innerHTML = `
            <div class="chat-notification-icon">
                ${initials}
            </div>
            <div class="chat-notification-content">
                <div class="chat-notification-header">
                    <span class="chat-notification-sender">${this.escapeHtml(sender)}</span>
                    <span class="chat-notification-time">${displayTime}</span>
                </div>
                <div class="chat-notification-message">${this.escapeHtml(message)}</div>
            </div>
            <button class="chat-notification-close" title="Tutup">
                <i class="fas fa-times"></i>
            </button>
        `;

        notification.addEventListener('click', (e) => {
            if (!e.target.closest('.chat-notification-close')) {
                this.openChat(senderId, sender);
                this.remove(id);
            }
        });

        const closeBtn = notification.querySelector('.chat-notification-close');
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.remove(id);
        });

        this.container.appendChild(notification);
        this.notifications.set(id, notification);

        if (this.soundEnabled && this.audioUnlocked) {
            this.playNotificationSound();
        }

        setTimeout(() => {
            this.remove(id);
        }, 10000);
    }

    showPushNotification(sender, message, senderId) {
        if (!this.pushNotificationEnabled || Notification.permission !== 'granted') {
            return;
        }

        try {
            const notification = new Notification(`?? ${sender}`, {
                body: message.length > 100 ? message.substring(0, 100) + '...' : message,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: `chat-${senderId}`,
                requireInteraction: false,
                silent: false,
                vibrate: [200, 100, 200],
                data: {
                    senderId: senderId,
                    sender: sender
                }
            });

            notification.onclick = (e) => {
                e.preventDefault();
                window.focus();
                this.openChat(senderId, sender);
                notification.close();
            };

            setTimeout(() => notification.close(), 10000);
        } catch (error) {}
    }

    remove(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        notification.classList.add('closing');

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 300);
    }

    openChat(userId, userName) {
        window.location.href = `/dashboard/admin/chat?user=${userId}`;
    }

    playNotificationSound() {
        if (!this.audioUnlocked) {
            return;
        }

        try {
            if (this.preloadedAudio) {
                this.preloadedAudio.currentTime = 0;
                this.preloadedAudio.play().catch(() => {});
            } else {
                const audio = new Audio('/sounds/42289.mp3');
                audio.volume = 0.5;
                audio.play().catch(() => {});
            }
        } catch (error) {}
    }

    getInitials(name) {
        return name
            .split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    clear() {
        this.notifications.forEach((_, id) => this.remove(id));
    }
}

if (window.isAdmin) {
    window.chatNotifications = new ChatNotificationManager();

    window.testNotification = function() {
        window.chatNotifications.show({
            id: 999,
            sender: 'Test User',
            message: 'This is a test notification',
            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
            senderId: 'test-123'
        });
    };
}
