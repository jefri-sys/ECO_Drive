<script>
    const limit = 5;
    let offset = 0, allLoaded = false;

    function fetchNotifications(params = {}) {
        return fetch(`/S6 PROJECT(TEAM 6)/Notification/notification.php?${new URLSearchParams(params)}`)
            .then(res => res.json());
    }

    function updateUI(data) {
        const badge = document.getElementById('notificationBadge');
        badge.textContent = data.unread_count;
        badge.classList.toggle('hidden', data.unread_count === 0);

        const list = document.getElementById('notificationList');
        if (offset === 0) list.innerHTML = '';
        
        if (data.notifications.length) {
            data.notifications.forEach(n => {
                list.innerHTML += `
                    <div class="p-4 border-b border-gray-100 ${n.is_read ? '' : 'bg-blue-50'}">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3">
                                <i class="ri-${n.type === 'service_completed' ? 'checkbox-circle' : 'time'}-line"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm ${n.is_read ? '' : 'font-semibold'}">${n.message}</p>
                                <p class="text-xs text-gray-500 mt-1">${n.formatted_date}</p>
                            </div>
                            ${n.is_read ? '' : `<button onclick="markRead(${n.id})" class="text-xs text-primary hover:underline">Mark as read</button>`}
                        </div>
                    </div>
                `;
            });
            offset += data.notifications.length;
            allLoaded = data.notifications.length < limit;
        } else if (offset === 0) {
            list.innerHTML = '<div class="p-4 text-center text-gray-500">No notifications</div>';
        }
    }

    function loadNotifications() {
        fetchNotifications({ action: 'get', limit, offset })
            .then(data => data.success ? updateUI(data) : console.error(data.message))
            .catch(err => console.error('Error:', err));
    }

    function loadMore() {
        if (!allLoaded) loadNotifications();
    }

    function markRead(id) {
        fetch('/S6 PROJECT(TEAM 6)/Notification/notification.php?action=mark', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `notification_id=${id}`
        }).then(res => res.json()).then(data => data.success && (offset = 0, loadNotifications()));
    }

    function markAllRead() {
        fetch('/S6 PROJECT(TEAM 6)/Notification/notification.php?action=mark', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'mark_all=true'
        }).then(res => res.json()).then(data => data.success && (offset = 0, loadNotifications()));
    }

    function toggleNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            offset = 0;
            loadNotifications();
            document.addEventListener('click', function handler(event) {
                if (!event.target.closest('.cursor-pointer') && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                    document.removeEventListener('click', handler);
                }
            });
        }
    }

    // Initial load and periodic check
    loadNotifications();
    setInterval(() => {
        fetchNotifications({ action: 'get', limit: 0 })
            .then(data => data.success && updateUI({ ...data, notifications: [] }))
            .catch(err => console.error('Error:', err));
    }, 30000);
</script>