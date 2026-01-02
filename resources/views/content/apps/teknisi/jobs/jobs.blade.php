@extends('layouts/layoutMaster')

@section('title', 'Daftar Ticket Teknisi')

@section('content')
<div class="container my-5">

  <h4 class="fw-bold mb-2 text-center">Daftar Ticket Teknisi</h4>
  <p class="text-center text-muted mb-5">Kelola dan pantau ticket pelanggan Anda</p>

  <!-- Filter Buttons -->
  <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
    @php
        $levels = [
            'urgent' => 'Urgent',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    @endphp

    @foreach($levels as $level => $label)
      <button class="btn-filter" data-level="{{ $level }}">
        {{ $label }}
        @if(isset($tickets[$level]) && $tickets[$level]->count() > 0)
          <span class="filter-badge">{{ $tickets[$level]->count() }}</span>
        @endif
      </button>
    @endforeach

    <button class="btn-filter active" data-level="all">
      Semua
    </button>
  </div>

  <!-- Ticket Cards -->
  <div id="ticket-container">
    @php
        $hasTickets = false;
        foreach($levels as $level => $label) {
            if(isset($tickets[$level]) && $tickets[$level]->count()) {
                $hasTickets = true;
                break;
            }
        }
    @endphp

    @if($hasTickets)
      @foreach($levels as $level => $label)
        @if(isset($tickets[$level]) && $tickets[$level]->count())
          <div class="ticket-group" data-level="{{ $level }}">
            <div class="section-header mb-4">
              <h5 class="section-title">{{ $label }}</h5>
              <span class="section-count">{{ $tickets[$level]->count() }} Ticket</span>
            </div>

            <div class="row g-4">
              @foreach($tickets[$level] as $ticket)
                <div class="col-12 col-md-6 col-lg-4 ticket-card" data-ticket-id="{{ $ticket->id }}">
                  <div class="card-modern">
                    <div class="card-header-modern">
                      <div class="customer-name">{{ $ticket->pelanggan->nama_lengkap }}</div>
                      <span class="status-badge status-{{ $ticket->status }}">
                        {{ ucfirst($ticket->status) }}
                      </span>
                    </div>
                    
                    <div class="card-body-modern">
                      <div class="info-row">
                        <span class="label">Masalah</span>
                        <p class="value">{{ \Illuminate\Support\Str::limit($ticket->issue_description, 60) }}</p>
                      </div>

                      @if($ticket->additional_note)
                        <div class="info-row">
                          <span class="label">Catatan</span>
                          <p class="value">{{ \Illuminate\Support\Str::limit($ticket->additional_note, 60) }}</p>
                        </div>
                      @endif

                      @if($ticket->phone)
                        <div class="info-row">
                          <span class="label">WhatsApp</span>
                          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->phone) }}" target="_blank" class="link-modern">
                            {{ $ticket->phone }}
                          </a>
                        </div>
                      @endif

                      <div class="info-row">
                        <span class="label">Lokasi</span>
                        @if($ticket->location_link)
                          <a href="{{ $ticket->location_link }}" target="_blank" class="link-modern">
                            Lihat peta ?
                          </a>
                        @else
                          <span class="text-muted">Tidak tersedia</span>
                        @endif
                      </div>

                      <div class="info-row">
                        <span class="label">Teknisi</span>
                        <span class="value">{{ $ticket->user->name ?? '-' }}</span>
                      </div>

                      <div class="info-row">
                        <span class="label">Dibuat</span>
                        <span class="value">{{ $ticket->created_at->format('d M Y') }}</span>
                      </div>

                      @if($ticket->attachment)
                        <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="btn-modern-secondary w-100 mb-2">
                          Lihat Foto
                        </a>
                      @endif

                      <div class="action-buttons">
                        @if(in_array($ticket->status, ['pending', 'assigned']))
                          <form action="{{ route('jobs.autoUpdateStatus', $ticket->id) }}" method="POST" class="w-100 mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="progress">
                            <button type="submit" class="btn-modern-primary w-100">
                              Mulai Pengerjaan
                            </button>
                          </form>
                        @elseif($ticket->status === 'progress')
                          <form action="{{ route('jobs.autoUpdateStatus', $ticket->id) }}" method="POST" class="w-100 mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="finished">
                            <button type="submit" class="btn-modern-primary w-100">
                              Tandai Selesai
                            </button>
                          </form>
                        @endif

                        <a href="{{ route('jobs.edit', $ticket->id) }}" class="btn-modern-outline w-100">
                          Edit Detail
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    @else
      <div class="empty-state">
        <p>Tidak ada ticket saat ini</p>
      </div>
    @endif
  </div>
</div>

{{-- Script Filter --}}
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('.btn-filter');
  const groups = document.querySelectorAll('.ticket-group');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const level = btn.dataset.level;
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      groups.forEach(group => {
        if (level === 'all' || group.dataset.level === level) {
          group.style.display = '';
        } else {
          group.style.display = 'none';
        }
      });
    });
  });

  // Realtime Notification via Pusher
  const userId = "{{ auth()->id() }}";
  Pusher.logToConsole = false;

  const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
      cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
      forceTLS: true
  });

  const channel = pusher.subscribe('private-jobs.' + userId);
  channel.bind('App\\Events\\TicketCreated', function(data) {
      console.log('Ticket baru:', data);
      new Audio('/sounds/notification.mp3').play();

      const container = document.getElementById('ticket-container');
      const cardHtml = `
      <div class="col-12 col-md-6 col-lg-4 ticket-card">
        <div class="card-modern">
          <div class="card-header-modern">
            <div class="customer-name">${data.customer_name}</div>
            <span class="status-badge status-${data.status}">${data.status}</span>
          </div>
          <div class="card-body-modern">
            <div class="info-row">
              <span class="label">Masalah</span>
              <p class="value">${data.issue_description}</p>
            </div>
            <div class="info-row">
              <span class="label">Prioritas</span>
              <span class="value">${data.priority}</span>
            </div>
          </div>
        </div>
      </div>`;
      container.insertAdjacentHTML('afterbegin', cardHtml);
  });
});
</script>

<style>
/* Modern Black & White Theme */
:root {
  --color-black: #000000;
  --color-white: #ffffff;
  --color-gray-50: #fafafa;
  --color-gray-100: #f5f5f5;
  --color-gray-200: #e5e5e5;
  --color-gray-300: #d4d4d4;
  --color-gray-400: #a3a3a3;
  --color-gray-500: #737373;
  --color-gray-600: #525252;
  --color-gray-700: #404040;
  --color-gray-800: #262626;
  --color-gray-900: #171717;
}

/* Filter Buttons */
.btn-filter {
  padding: 10px 24px;
  border: 2px solid var(--color-gray-300);
  background: var(--color-white);
  color: var(--color-gray-700);
  font-weight: 500;
  font-size: 14px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.btn-filter:hover {
  border-color: var(--color-black);
  background: var(--color-gray-50);
}

.btn-filter.active {
  background: var(--color-black);
  color: var(--color-white);
  border-color: var(--color-black);
}

.filter-badge {
  display: inline-block;
  background: var(--color-gray-200);
  color: var(--color-gray-700);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  margin-left: 6px;
}

.btn-filter.active .filter-badge {
  background: var(--color-gray-700);
  color: var(--color-white);
}

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--color-gray-200);
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-black);
  margin: 0;
}

.section-count {
  background: var(--color-gray-100);
  color: var(--color-gray-600);
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
}

/* Card Design */
.card-modern {
  background: var(--color-white);
  border: 1px solid var(--color-gray-200);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.card-modern:hover {
  border-color: var(--color-gray-400);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.card-header-modern {
  padding: 20px;
  background: var(--color-gray-50);
  border-bottom: 1px solid var(--color-gray-200);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.customer-name {
  font-weight: 600;
  font-size: 15px;
  color: var(--color-black);
}

.status-badge {
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-finished {
  background: var(--color-black);
  color: var(--color-white);
}

.status-progress {
  background: var(--color-gray-700);
  color: var(--color-white);
}

.status-pending {
  background: var(--color-gray-200);
  color: var(--color-gray-700);
}

.status-assigned {
  background: var(--color-gray-300);
  color: var(--color-gray-900);
}

.card-body-modern {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.info-row {
  margin-bottom: 16px;
}

.info-row .label {
  display: block;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--color-gray-500);
  margin-bottom: 4px;
  font-weight: 600;
}

.info-row .value {
  color: var(--color-gray-900);
  font-size: 14px;
  margin: 0;
  line-height: 1.5;
}

.link-modern {
  color: var(--color-black);
  text-decoration: none;
  font-size: 14px;
  border-bottom: 1px solid var(--color-gray-300);
  transition: all 0.2s ease;
}

.link-modern:hover {
  border-bottom-color: var(--color-black);
}

/* Buttons */
.btn-modern-primary {
  padding: 12px 20px;
  background: var(--color-black);
  color: var(--color-white);
  border: none;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-modern-primary:hover {
  background: var(--color-gray-800);
  transform: translateY(-1px);
}

.btn-modern-secondary {
  padding: 10px 20px;
  background: var(--color-gray-100);
  color: var(--color-gray-900);
  border: none;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  transition: all 0.2s ease;
}

.btn-modern-secondary:hover {
  background: var(--color-gray-200);
}

.btn-modern-outline {
  padding: 10px 20px;
  background: transparent;
  color: var(--color-gray-900);
  border: 2px solid var(--color-gray-300);
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  transition: all 0.2s ease;
}

.btn-modern-outline:hover {
  border-color: var(--color-black);
  background: var(--color-gray-50);
}

.action-buttons {
  margin-top: auto;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: var(--color-gray-50);
  border: 2px dashed var(--color-gray-300);
  border-radius: 12px;
  color: var(--color-gray-500);
}

.empty-state p {
  margin: 0;
  font-size: 15px;
}

/* Responsive */
@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>

@endsection
