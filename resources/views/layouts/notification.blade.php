@auth
    @php
        $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
        $recentNotifications = Auth::user()->notifications()->latest()->take(5)->get();
    @endphp

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell"></i>
            @if($unreadNotificationsCount > 0)
                <span class="badge badge-danger navbar-badge">{{ $unreadNotificationsCount }}</span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end" aria-labelledby="notificationDropdown">
            @if($recentNotifications->isEmpty())
                <a href="#" class="dropdown-item text-center">
                    <p class="text-muted text-sm mb-0">Aucune nouvelle notification</p>
                </a>
            @else
                @foreach($recentNotifications as $notification)
                    <a href="{{ route('notifications.show', $notification->id) }}" class="dropdown-item">
                        <div class="media">
                            {{-- Example: Icon for stock low notification --}}
                            @if(isset($notification->data['article_id']))
                                <i class="fas fa-boxes mr-2 mt-1"></i>
                            @else
                                <i class="fas fa-info-circle mr-2 mt-1"></i> {{-- Default icon --}}
                            @endif
                            <div class="media-body">
                                <h3 class="dropdown-item-title" style="font-size: 0.9rem; white-space: normal;">
                                    {{ Str::limit($notification->data['message'], 60) }}
                                    @if(!$notification->read_at)
                                        <span class="float-right text-xs text-danger"><i class="fas fa-circle" style="font-size: 0.5rem;"></i></span>
                                    @endif
                                </h3>
                                <p class="text-xs text-muted"><i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans(null, true, true) }}</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider my-0"></div>
                @endforeach
            @endif
            <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer text-center">Voir toutes les notifications</a>
        </div>
    </li>
@endauth

