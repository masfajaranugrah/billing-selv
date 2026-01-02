@extends('layouts/layoutMaster')

@section('title', 'Chat Admin')

@use('Illuminate\Support\Facades\Auth')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('vendor-script')
    @vite(['resources/js/bootstrap.js', 'resources/js/echo.js'])
@endsection

@section('page-style')
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .admin-chat-container {
            display: flex;
            width: 100%;
            height: calc(100vh - 120px);
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 20px auto;
            max-width: 1400px;
            border: 1px solid #e5e7eb;
        }

        .users-sidebar {
            width: 380px;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }

        .sidebar-header {
            background: #1e293b;
            color: #ffffff;
            padding: 28px 24px;
        }

        .admin-avatar {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .admin-info h2 {
            font-size: 20px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #ffffff;
        }

        .admin-status {
            font-size: 13px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .search-box {
            padding: 16px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .search-wrapper {
            position: relative;
        }

        .chat-search-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            background: #f8fafc;
            color: #1e293b;
        }

        .chat-search-input::placeholder {
            color: #94a3b8;
        }

        .chat-search-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .user-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            background: #f8fafc;
        }

        .user-list::-webkit-scrollbar {
            width: 6px;
        }

        .user-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .user-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .user-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .user-item {
            padding: 14px 12px;
            margin-bottom: 6px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            background: #ffffff;
            border: 1px solid transparent;
        }

        .user-item:hover {
            background: #f1f5f9;
            border-color: #e5e7eb;
            transform: translateX(2px);
        }

        .user-item.active {
            background: #eff6ff;
            border-color: #3b82f6;
            box-shadow: 0 1px 3px rgba(59, 130, 246, 0.1);
        }

        .user-item-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            font-size: 18px;
            flex-shrink: 0;
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 3px;
            font-size: 15px;
            color: #1e293b;
        }

        .user-type {
            font-size: 12px;
            color: #64748b;
        }

        .unread-badge {
            background: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }

        .chat-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

        .chat-header {
            background: #ffffff;
            color: #1e293b;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #ffffff;
        }

        .chat-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
            color: #1e293b;
        }

        .chat-subtitle {
            font-size: 12px;
            color: #10b981;
            font-weight: 500;
        }

        .chat-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #f8fafc;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .message {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            animation: slideIn 0.2s ease;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #ffffff;
            font-weight: 600;
            flex-shrink: 0;
        }

        .message.sent .message-avatar {
            background: #3b82f6;
            order: 2;
        }

        .message.received .message-avatar {
            background: #10b981;
        }

        .message-bubble {
            max-width: 60%;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
        }

        .message.sent .message-content {
            background: #3b82f6;
            color: #ffffff;
            border-bottom-right-radius: 4px;
        }

        .message.received .message-content {
            background: #ffffff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            border: 1px solid #e5e7eb;
        }

        .message-text {
            font-size: 14px;
            line-height: 1.5;
        }

        .message-info {
            font-size: 11px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .message.sent .message-info {
            justify-content: flex-end;
            color: rgba(255, 255, 255, 0.8);
        }

        .message.received .message-info {
            color: #94a3b8;
        }

        .chat-input-container {
            padding: 16px 20px;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            margin-bottom: 60px;
        }

        .chat-input-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .input-wrapper {
            flex: 1;
            position: relative;
        }

        .chat-input {
            width: 100%;
            padding: 12px 50px 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
            color: #1e293b;
            resize: none;
            max-height: 100px;
        }

        .chat-input::placeholder {
            color: #94a3b8;
        }

        .chat-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .emoji-button {
            position: absolute;
            right: 14px;
            bottom: 14px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.2s;
        }

        .emoji-button:hover {
            color: #3b82f6;
            transform: scale(1.1);
        }

        .attach-button {
            width: 44px;
            height: 44px;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .attach-button:hover {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .media-preview-container {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .remove-media-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-filename {
            font-size: 12px;
            color: #64748b;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .send-button {
            width: 44px;
            height: 44px;
            background: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .send-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .send-button:active {
            transform: translateY(0);
        }

        .send-button:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }

        .no-chat-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 16px;
        }

        .no-chat-icon {
            font-size: 64px;
            color: #cbd5e1;
        }

        .no-chat-text {
            color: #475569;
            font-size: 18px;
            font-weight: 600;
        }

        .no-chat-subtext {
            color: #94a3b8;
            font-size: 14px;
        }

        .date-divider {
            text-align: center;
            margin: 20px 0;
        }

        .date-text {
            display: inline-block;
            padding: 6px 14px;
            background: #e2e8f0;
            color: #64748b;
            font-size: 12.5px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
    </style>
@endsection

@section('content')
    <div class="admin-chat-container">
        <div class="users-sidebar">
            <div class="sidebar-header">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2>Admin Panel</h2>
                    <div class="admin-status">
                        <span class="status-dot"></span>
                        <span>Customer Service</span>
                    </div>
                </div>
            </div>

            <div class="search-box">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="chat-search-input" id="chatSearchInput" placeholder="Cari user..."
                        autocomplete="off">
                </div>
            </div>

            <div class="user-list" id="userList">
                @foreach($users as $user)
                    <div class="user-item" data-user-id="{{ $user['id'] }}" data-user-name="{{ $user['name'] }}">
                        <div class="user-item-content">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user['name'], 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ $user['name'] }}</div>
                                <div class="user-type">{{ ($user['nomer_id']) }}</div>
                            </div>
                            <span class="unread-badge" id="unread-{{ $user['id'] }}" style="display: none;">0</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="chat-section">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar" id="chatAvatar" style="display: none;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h1 class="chat-title" id="chatTitle">Pilih user untuk memulai chat</h1>
                        <div class="chat-subtitle" id="chatSubtitle" style="display: none;">? Online</div>
                    </div>
                </div>
                <div class="chat-actions" id="chatActions" style="display: none;">
                    <button class="action-btn" title="Info">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    <button class="action-btn" title="More">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="no-chat-selected">
                    <i class="fas fa-comments no-chat-icon"></i>
                    <div class="no-chat-text">Selamat Datang, Admin!</div>
                    <div class="no-chat-subtext">Pilih user dari sidebar untuk memulai percakapan</div>
                </div>
            </div>

            <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                <div id="mediaPreview"
                    style="display: none; padding: 8px 12px; background: #f1f5f9; border-radius: 8px; margin-bottom: 8px;">
                </div>
                <form class="chat-input-form" id="chatForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="receiverId" name="receiver_id">
                    <input type="file" id="mediaInput" accept="image/*,video/*" style="display: none;">
                    <button type="button" class="attach-button" id="attachButton" title="Kirim foto/video">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <div class="input-wrapper">
                        <input type="text" class="chat-input" id="messageInput" placeholder="Tulis pesan Anda..."
                            autocomplete="off">
                        <button type="button" class="emoji-button">😊</button>
                    </div>
                    <button type="submit" class="send-button" id="sendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        window.userId = "{{ Auth::id() }}";
        window.userName = "{{ Auth::user()->name }}";
        window.isAdmin = true;
        window.selectedUserId = null;

    </script>
    @vite(['resources/js/chat.js'])
@endsection