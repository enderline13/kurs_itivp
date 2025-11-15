document.addEventListener('DOMContentLoaded', function () {
    var apiBase = '/api.php';
    var appState = { owners: [] };
    var dayNames = ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"];

    async function checkAuth() {
        try {
            var resp = await fetch(apiBase + '?file=auth&action=check');
            if (!resp.ok) return;
            var json = await resp.json();
            var els = {
                login: document.getElementById('nav-login'),
                register: document.getElementById('nav-register'),
                profile: document.getElementById('nav-profile'),
                logout: document.getElementById('nav-logout'),
                admin: document.getElementById('nav-admin'),
                owner: document.getElementById('nav-owner')
            };
            if (json.loggedIn) {
                if (els.login) els.login.style.display = 'none';
                if (els.register) els.register.style.display = 'none';
                if (els.profile) els.profile.style.display = 'inline';
                if (els.logout) els.logout.style.display = 'inline';
                if (json.role === 'admin' && els.admin) {
                    els.admin.style.display = 'inline';
                }
                if (json.role === 'owner' && els.owner) {
                    els.owner.style.display = 'inline';
                }
            } else {
                if (els.login) els.login.style.display = 'inline';
                if (els.register) els.register.style.display = 'inline';
                if (els.profile) els.profile.style.display = 'none';
                if (els.logout) els.logout.style.display = 'none';
                if (els.admin) els.admin.style.display = 'none';
                if (els.owner) els.owner.style.display = 'none';
            }
        } catch (e) {}
    }
    
    checkAuth();
    
    var logoutLink = document.getElementById('nav-logout');
    if (logoutLink) {
        logoutLink.addEventListener('click', async function (e) {
            e.preventDefault();
            try {
                var resp = await fetch(apiBase + '?file=auth&action=logout', { method: 'POST' });
                var json = await resp.json();
                if (json.success) {
                    window.location.href = 'index.php';
                }
            } catch (err) {
                alert('Ошибка выхода');
            }
        });
    }

    var loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(loginForm);
            var msgEl = document.getElementById('login-msg');
            msgEl.textContent = 'Отправка...';
            try {
                var resp = await fetch(apiBase + '?file=auth&action=login', {
                    method: 'POST',
                    body: data
                });
                if (!resp.ok) throw new Error('Сервер вернул ошибку');
                var json = await resp.json();
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Успешно. Перенаправление...';
                    window.location.href = json.redirect || 'profile.php';
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка входа';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Бэкенд недоступен. Позже попробуйте снова.';
            }
        });
    }

    var registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(registerForm);
            var msgEl = document.getElementById('register-msg');
            msgEl.textContent = 'Отправка...';
            try {
                var resp = await fetch(apiBase + '?file=auth&action=register', {
                    method: 'POST',
                    body: data
                });
                if (!resp.ok) throw new Error('Сервер вернул ошибку');
                var json = await resp.json();
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Аккаунт создан. Перенаправление...';
                    window.location.href = json.redirect || 'login.php';
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка регистрации';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Бэкенд недоступен. Позже попробуйте снова.';
            }
        });
    }

    var searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(searchForm);
            var params = new URLSearchParams();
            for (var pair of data.entries()) params.append(pair[0], pair[1]);
            var list = document.getElementById('restaurants-list');
            list.innerHTML = '<div class="restaurant-card placeholder"><h3>Загрузка...</h3></div>';
            try {
                var resp = await fetch(apiBase + '?file=restaurants&' + params.toString());
                if (!resp.ok) throw new Error('Сервер вернул ошибку');
                var json = await resp.json();
                renderRestaurants(json);
            } catch (err) {
                list.innerHTML = '<div class="restaurant-card placeholder">Бэкенд недоступен. Невозможно загрузить список.</div>';
            }
        });
    }

    var loadAllBtn = document.getElementById('load-all');
    if (loadAllBtn) {
        loadAllBtn.addEventListener('click', async function () {
            var list = document.getElementById('restaurants-list');
            list.innerHTML = '<div class="restaurant-card placeholder"><h3>Загрузка...</h3></div>';
            try {
                var resp = await fetch(apiBase + '?file=restaurants');
                if (!resp.ok) throw new Error('Сервер вернул ошибку');
                var json = await resp.json();
                renderRestaurants(json);
            } catch (err) {
                list.innerHTML = '<div class="restaurant-card placeholder">Бэкенд недоступен. Невозможно загрузить список.</div>';
            }
        });
    }

    function renderRestaurants(data) {
        var list = document.getElementById('restaurants-list');
        if (!Array.isArray(data) || data.length === 0) {
            list.innerHTML = '<div class="restaurant-card">Рестораны не найдены.</div>';
            return;
        }
        list.innerHTML = '';
        data.forEach(function (r) {
            var el = document.createElement('article');
            el.className = 'restaurant-card';
            var html = '<div class="card-row"><div class="card-content">';
            html += '<h3>' + escapeHtml(r.name) + '</h3>';
            html += '<p>Адрес: ' + escapeHtml(r.address || '') + '</p>';
            html += '<p>' + (r.description ? escapeHtml(r.description) : '') + '</p>';
            html += '</div><div class="card-actions">';
            html += '<a class="button" href="restaurant.php?id=' + encodeURIComponent(r.id) + '">Открыть</a>';
            html += '</div></div>';
            el.innerHTML = html;
            list.appendChild(el);
        });
    }

    var reservationForm = document.getElementById('reservation-form');
    if (reservationForm) {
        fillReservationRestaurants();
        reservationForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(reservationForm);
            var msgEl = document.getElementById('reservation-msg');
            msgEl.textContent = 'Отправка брони...';
            try {
                var resp = await fetch(apiBase + '?file=booking&action=create', {
                    method: 'POST',
                    body: data
                });
                if (!resp.ok) throw new Error('Сервер вернул ошибку');
                var json = await resp.json();
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Бронь создана';
                    window.location.href = json.redirect || 'profile.php';
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка создания брони';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Бэкенд недоступен. Позже попробуйте снова.';
            }
        });

        var restSelect = document.getElementById('reservation-restaurant');
        if (restSelect) {
            restSelect.addEventListener('change', function () {
                var rid = restSelect.value;
                fillTablesForRestaurant(rid);
            });
        }
        
        var tableSelect = document.getElementById('reservation-table');
        var dateInput = document.getElementById('reservation-date');
        var timeInput = document.getElementById('reservation-time');

        async function checkTableAvailability() {
            var tableId = tableSelect.value;
            var date = dateInput.value;
            var time = timeInput.value;
            var msgEl = document.getElementById('reservation-msg');
            if (!tableId || !date || !time) {
                msgEl.textContent = '';
                return;
            }
            msgEl.style.color = '#555';
            msgEl.textContent = 'Проверка доступности...';
            try {
                var params = new URLSearchParams({
                    file: 'booking',
                    action: 'check_availability',
                    table_id: tableId,
                    date: date,
                    time: time
                });
                var resp = await fetch(apiBase + '?' + params.toString());
                var json = await resp.json();
                if (json.available) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Столик свободен на это время!';
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = 'Столик уже занят на это время.';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Не удалось проверить доступность.';
            }
        }

        tableSelect.addEventListener('change', checkTableAvailability);
        dateInput.addEventListener('change', checkTableAvailability);
        timeInput.addEventListener('change', checkTableAvailability);
    }

    async function fillReservationRestaurants() {
        var sel = document.getElementById('reservation-restaurant');
        if (!sel) return;
        try {
            var resp = await fetch(apiBase + '?file=restaurants');
            if (!resp.ok) throw new Error('Сервер вернул ошибку');
            var json = await resp.json();
            sel.innerHTML = '<option value="">Выберите ресторан</option>';
            json.forEach(function (r) {
                var opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name + ' — ' + (r.city || '');
                sel.appendChild(opt);
            });
        } catch (err) {
            sel.innerHTML = '<option value="">Невозможно загрузить</option>';
        }
    }

    async function fillTablesForRestaurant(restaurantId) {
        var sel = document.getElementById('reservation-table');
        if (!sel) return;
        if (!restaurantId) {
            sel.innerHTML = '<option value="">Выберите столик</option>';
            return;
        }
        try {
            var resp = await fetch(apiBase + '?file=tables&restaurant_id=' + encodeURIComponent(restaurantId));
            if (!resp.ok) throw new Error('Сервер вернул ошибку');
            var json = await resp.json();
            sel.innerHTML = '';
            json.forEach(function (t) {
                var opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = 'Стол №' + t.id + ' — ' + t.seats + ' мест';
                sel.appendChild(opt);
            });
        } catch (err) {
            sel.innerHTML = '<option value="">Невозможно загрузить</option>';
        }
    }

    var profilePage = document.getElementById('profile-data');
    if (profilePage) {
        loadProfile();
    }

    async function loadProfile() {
        var profileMsg = document.getElementById('profile-msg');
        try {
            var resp = await fetch(apiBase + '?file=profile');
            if (!resp.ok) throw new Error('Сервер вернул ошибку');
            var json = await resp.json();
            if (!json.user) {
                profileMsg.textContent = 'Неавторизованный пользователь';
                return;
            }
            var p = document.getElementById('profile-data');
            p.innerHTML = '<p><strong>Имя:</strong> ' + escapeHtml(json.user.full_name || '') + '</p>'
                + '<p><strong>Email:</strong> ' + escapeHtml(json.user.email || '') + '</p>'
                + '<p><strong>Телефон:</strong> ' + escapeHtml(json.user.phone || '') + '</p>';
            renderBookings(json.bookings || []);
        } catch (err) {
            profileMsg.textContent = 'Бэкенд недоступен. Невозможно загрузить профиль.';
            var bookings = document.getElementById('my-bookings');
            bookings.innerHTML = '<div class="restaurant-card placeholder">Невозможно загрузить бронирования</div>';
        }
    }

    function renderBookings(list) {
        var cont = document.getElementById('my-bookings');
        cont.innerHTML = '';
        if (!Array.isArray(list) || list.length === 0) {
            cont.innerHTML = '<div class="restaurant-card">Бронирований нет.</div>';
            return;
        }
        list.forEach(function (b) {
            var el = document.createElement('div');
            el.className = 'restaurant-card';
            el.innerHTML = '<div class="card-row"><div class="card-content"><strong>' + escapeHtml(b.restaurant_name || '—') + '</strong><p>Дата: ' + escapeHtml(b.date || '') + '</p><p>Время: ' + escapeHtml(b.time || '') + '</p><p>Стол: ' + escapeHtml(String(b.table_id || '—')) + ' · Гости: ' + escapeHtml(String(b.guests || '—')) + '</p><p>Статус: ' + escapeHtml(b.status || '—') + '</p></div><div class="card-actions"><button class="button" data-id="' + escapeHtml(b.id) + '">Отменить</button></div></div>';
            cont.appendChild(el);
        });
        var buttons = cont.querySelectorAll('button[data-id]');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', async function () {
                var id = btn.getAttribute('data-id');
                try {
                    var resp = await fetch(apiBase + '?file=booking&action=cancel', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    });
                    if (!resp.ok) throw new Error('Сервер вернул ошибку');
                    var json = await resp.json();
                    if (json.success) {
                        loadProfile();
                    } else {
                        alert(json.message || 'Не удалось отменить бронь');
                    }
                } catch (err) {
                    alert('Бэкенд недоступен. Невозможно отменить бронь');
                }
            });
        });
    }

    var restaurantsPage = document.getElementById('restaurants-list');
    if (restaurantsPage && document.getElementById('search-form')) {
        try {
            document.getElementById('load-all').click();
        } catch (e) {}
    }

    var restaurantPageHeader = document.getElementById('r-name');
    if (restaurantPageHeader) {
        loadRestaurantPage();
    }

    async function loadRestaurantPage() {
        var params = new URLSearchParams(window.location.search);
        var id = params.get('id');
        var msgEl = document.getElementById('restaurant-msg');
        try {
            if (!id) throw new Error('no id');
            var resp = await fetch(apiBase + '?file=restaurants&id=' + encodeURIComponent(id));
            if (!resp.ok) throw new Error('Сервер вернул ошибку');
            var json = await resp.json();
            if (!json || !json.id) throw new Error('not found');
            document.getElementById('r-name').textContent = json.name || '—';
            document.getElementById('r-address').textContent = 'Адрес: ' + (json.address || '');
            document.getElementById('r-city').textContent = 'Город: ' + (json.city || '');
            document.getElementById('r-text').textContent = json.description || '';
            
            var tablesList = document.getElementById('tables-list');
            tablesList.innerHTML = '';
            (json.tables || []).forEach(function (t) {
                var el = document.createElement('div');
                el.className = 'restaurant-card';
                el.innerHTML = '<div class="card-row"><div class="card-content"><strong>Стол №' + escapeHtml(String(t.id)) + '</strong><p>Места: ' + escapeHtml(String(t.seats)) + '</p></div><div class="card-actions"><a class="button" href="reservation.php?restaurant_id=' + encodeURIComponent(json.id) + '&table_id=' + encodeURIComponent(t.id) + '">Забронировать</a></div></div>';
                tablesList.appendChild(el);
            });
            
            var hoursCont = document.getElementById('r-hours');
            if (hoursCont && json.opening_hours) {
                hoursCont.innerHTML = '';
                json.opening_hours.forEach(function (h) {
                    var el = document.createElement('p');
                    var day = dayNames[h.weekday] || '';
                    var time = h.is_closed ? 'Закрыто' : (h.open_time + ' - ' + h.close_time);
                    el.innerHTML = '<strong>' + escapeHtml(day) + ':</strong> ' + escapeHtml(time);
                    hoursCont.appendChild(el);
                });
            }

        } catch (err) {
            if (msgEl) msgEl.textContent = 'Невозможно загрузить данные ресторана';
        }
    }

    var adminPage = document.getElementById('admin-users');
    if (adminPage) {
        loadAdminDashboard();
    }
    
    var addUserForm = document.getElementById('add-user-form');
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(addUserForm);
            data.append('action', 'add_user');
            var msgEl = document.getElementById('add-user-msg');
            msgEl.textContent = 'Создание...';
            
            try {
                var resp = await fetch(apiBase + '?file=admin', { method: 'POST', body: data });
                var json = await resp.json();
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Пользователь создан.';
                    addUserForm.reset();
                    loadAdminDashboard(); 
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Бэкенд недоступен';
            }
        });
    }

    async function loadAdminDashboard() {
        var contUsers = document.getElementById('admin-users');
        var contRest = document.getElementById('admin-restaurants');
        try {
            var ownerResp = await fetch(apiBase + '?file=admin&action=get_owners');
            appState.owners = await ownerResp.json();
            
            var resp = await fetch(apiBase + '?file=admin&action=get_data');
            if (!resp.ok) throw new Error('Сервер вернул ошибку');
            var json = await resp.json();
            if(json.success === false) throw new Error(json.message);
            
            // 3. Рендер
            renderAdminUsers(json.users || []);
            renderAdminRestaurants(json.restaurants || []);
            
            contUsers.querySelectorAll('.admin-delete-btn').forEach(function(btn) {
                btn.addEventListener('click', handleDeleteAdmin);
            });
            contRest.querySelectorAll('.admin-delete-btn').forEach(function(btn) {
                btn.addEventListener('click', handleDeleteAdmin);
            });
            
            contRest.querySelectorAll('.admin-owner-toggle-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var id = e.target.dataset.id;
                    var form = document.getElementById('owner-form-' + id);
                    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
                });
            });
            contRest.querySelectorAll('.admin-owner-save-btn').forEach(function(btn) {
                btn.addEventListener('click', handleChangeOwner);
            });


        } catch (err) {
            contUsers.innerHTML = '<div class="restaurant-card placeholder">Невозможно загрузить</div>';
            contRest.innerHTML = '<div class="restaurant-card placeholder">Невозможно загрузить</div>';
        }
    }

    async function handleChangeOwner(e) {
        var btn = e.target;
        var rId = btn.dataset.id;
        var sel = document.getElementById('owner-select-' + rId);
        var newOwnerId = sel.value;
        
        if (!newOwnerId) {
            alert('Выберите владельца');
            return;
        }
        
        var data = new FormData();
        data.append('id', rId);
        data.append('owner_id', newOwnerId);
        data.append('action', 'change_owner');
        
        try {
            var resp = await fetch(apiBase + '?file=admin', { method: 'POST', body: data });
            var json = await resp.json();
            if (json.success) {
                alert('Владелец изменен');
                document.getElementById('owner-form-' + rId).style.display = 'none';
            } else {
                alert(json.message || 'Ошибка');
            }
        } catch (err) {
            alert('Бэкенд недоступен');
        }
    }

    async function handleDeleteAdmin(e) {
        var btn = e.target;
        var id = btn.dataset.id;
        var type = btn.dataset.type;
        var action = (type === 'user') ? 'delete_user' : 'delete_restaurant';
        if (!confirm('Вы уверены, что хотите удалить этот ' + type + '?')) return;
        try {
            var data = new FormData();
            data.append('id', id);
            data.append('action', action);
            var resp = await fetch(apiBase + '?file=admin', {
                method: 'POST',
                body: data
            });
            var json = await resp.json();
            if (json.success) {
                loadAdminDashboard();
            } else {
                alert('Ошибка: ' + (json.message || 'Не удалось удалить'));
            }
        } catch (err) {
            alert('Бэкенд недоступен');
        }
    }
    
    function renderAdminUsers(list) {
        var cont = document.getElementById('admin-users');
        cont.innerHTML = '';
        list.forEach(function (u) {
            var el = document.createElement('div');
            el.className = 'restaurant-card';
            el.innerHTML = '<div class="card-row"><div class="card-content"><strong>' + escapeHtml(u.full_name) + '</strong><p>' + escapeHtml(u.email) + '</p><p>Роль: ' + escapeHtml(u.role_name) + '</p></div><div class="card-actions"><button class="button outline admin-delete-btn" data-id="' + u.id + '" data-type="user">Удалить</button></div></div>';
            cont.appendChild(el);
        });
    }
    
    function renderAdminRestaurants(list) {
        var cont = document.getElementById('admin-restaurants');
        cont.innerHTML = '';
        
        var ownerOptions = '<option value="">Выберите владельца</option>';
        (appState.owners || []).forEach(function(o) {
            ownerOptions += '<option value="' + o.id + '">' + escapeHtml(o.full_name) + '</option>';
        });
        
        list.forEach(function (r) {
            var el = document.createElement('div');
            el.className = 'restaurant-card';
            var html = '<div class="card-row"><div class="card-content"><strong>' + escapeHtml(r.name) + '</strong><p>' + escapeHtml(r.city) + '</p></div>';
            html += '<div class="card-actions">';
            html += '<button class="button outline admin-delete-btn" data-id="' + r.id + '" data-type="restaurant">Удалить</button>';
            html += '<button class="button admin-owner-toggle-btn" data-id="' + r.id + '">Сменить владельца</button>';
            html += '</div></div>';
            
            html += '<div class="admin-owner-form" id="owner-form-' + r.id + '" style="display:none; margin-top:10px;">';
            html += '<select id="owner-select-' + r.id + '">' + ownerOptions + '</select> ';
            html += '<button class="button admin-owner-save-btn" data-id="' + r.id + '">Сохранить</button>';
            html += '</div>';
            
            el.innerHTML = html;
            cont.appendChild(el);
        });
    }
    
    var ownerPage = document.getElementById('owner-restaurants');
    if (ownerPage) {
        loadOwnerDashboard();
    }
    
    async function loadOwnerDashboard() {
        try {
            var resp = await fetch(apiBase + '?file=owner');
            if (!resp.ok) throw new Error('Сервер вернул ошибку');
            var json = await resp.json();
            if(json.success === false) throw new Error(json.message);
            renderOwnerRestaurants(json.restaurants || []);
            renderOwnerBookings(json.bookings || []);
            
            document.querySelectorAll('.owner-delete-btn').forEach(function(btn) {
                btn.addEventListener('click', async function(e) {
                    var id = e.target.dataset.id;
                    if (!confirm('Удалить этот ресторан? Это действие необратимо.')) return;
                    
                    var data = new FormData();
                    data.append('id', id);
                    data.append('action', 'delete');
                    
                    try {
                        var delResp = await fetch(apiBase + '?file=restaurant_manage', { method: 'POST', body: data });
                        var delJson = await delResp.json();
                        if (delJson.success) {
                            loadOwnerDashboard();
                        } else {
                            alert(delJson.message || 'Ошибка удаления');
                        }
                    } catch(err) {
                        alert('Бэкенд недоступен');
                    }
                });
            });
            
        } catch (err) {
            document.getElementById('owner-restaurants').innerHTML = '<div class="restaurant-card placeholder">Невозможно загрузить</div>';
            document.getElementById('owner-bookings').innerHTML = '<div class="restaurant-card placeholder">Невозможно загрузить</div>';
        }
    }
    
    function renderOwnerRestaurants(list) {
        var cont = document.getElementById('owner-restaurants');
        cont.innerHTML = '';
        if (list.length === 0) {
            cont.innerHTML = '<div class="restaurant-card">Рестораны не найдены.</div>';
            return;
        }
        list.forEach(function (r) {
            var el = document.createElement('div');
            el.className = 'restaurant-card';
            el.innerHTML = '<div class="card-row"><div class="card-content"><strong>' + escapeHtml(r.name) + '</strong><p>' + escapeHtml(r.address) + '</p></div><div class="card-actions">'
             + '<a class="button" href="restaurant_form.php?id=' + r.id + '">Редакт.</a>'
             + '<a class="button" href="hours_form.php?restaurant_id=' + r.id + '">Часы</a>' // Новая кнопка
             + '<a class="button outline" href="tables_list.php?restaurant_id=' + r.id + '">Столики</a>'
             + '<button class="button outline owner-delete-btn" data-id="' + r.id + '">Удалить</button>' // Новая кнопка
             + '</div></div>';
            cont.appendChild(el);
        });
    }

    function renderOwnerBookings(list) {
        var cont = document.getElementById('owner-bookings');
        cont.innerHTML = '';
        if (list.length === 0) {
            cont.innerHTML = '<div class="restaurant-card">Бронирований нет.</div>';
            return;
        }
        list.forEach(function (b) {
            var el = document.createElement('div');
            el.className = 'restaurant-card';
            el.innerHTML = '<div class="card-row"><div class="card-content"><strong>' + escapeHtml(b.restaurant_name || '—') + '</strong><p>Клиент: ' + escapeHtml(b.user_name || '—') + '</p><p>Дата: ' + escapeHtml(b.date || '') + ' ' + escapeHtml(b.time || '') + '</p><p>Стол: ' + escapeHtml(String(b.table_id || '—')) + ' · Гости: ' + escapeHtml(String(b.guests || '—')) + '</p><p>Статус: ' + escapeHtml(b.status || '—') + '</p></div></div>';
            cont.appendChild(el);
        });
    }

    var restaurantForm = document.getElementById('restaurant-form');
    if (restaurantForm) {
        var params = new URLSearchParams(window.location.search);
        var rId = params.get('id');
        if (rId) {
            document.getElementById('form-title').textContent = 'Редактировать ресторан';
            loadRestaurantForEdit(rId);
        }
        restaurantForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(restaurantForm);
            var msgEl = document.getElementById('form-msg');
            msgEl.textContent = 'Сохранение...';
            try {
                var resp = await fetch(apiBase + '?file=restaurant_manage', {
                    method: 'POST',
                    body: data
                });
                var json = await resp.json();
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Сохранено. Перенаправление...';
                    window.location.href = 'owner_dashboard.php';
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка сохранения';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Бэкенд недоступен';
            }
        });
    }

    async function loadRestaurantForEdit(id) {
        try {
            var resp = await fetch(apiBase + '?file=restaurants&id=' + id);
            var json = await resp.json();
            if (json.id) {
                document.getElementById('r-id').value = json.id;
                document.getElementById('r-name').value = json.name;
                document.getElementById('r-description').value = json.description;
                document.getElementById('r-address').value = json.address;
                document.getElementById('r-city').value = json.city;
            } else {
                document.getElementById('form-msg').textContent = json.message || 'Ресторан не найден';
            }
        } catch (err) {
            document.getElementById('form-msg').textContent = 'Не удалось загрузить данные';
        }
    }

    var tablesListPage = document.getElementById('tables-list-admin');
    if(tablesListPage) {
        var params = new URLSearchParams(window.location.search);
        var rId = params.get('restaurant_id');
        if (!rId) {
             tablesListPage.innerHTML = '<div class="restaurant-card">ID ресторана не указан</div>';
        } else {
            document.getElementById('add-table-btn').href = 'table_form.php?restaurant_id=' + rId;
            loadTablesForAdmin(rId);
            fetch(apiBase + '?file=restaurants&id=' + rId)
                .then(r => r.json())
                .then(r => { 
                    if(r.name) document.getElementById('r-name').textContent = r.name;
                });
        }
    }

    async function loadTablesForAdmin(restaurantId) {
        var cont = document.getElementById('tables-list-admin');
        cont.innerHTML = '<div class="restaurant-card placeholder">Загрузка столиков...</div>';
        try {
            var resp = await fetch(apiBase + '?file=tables&restaurant_id=' + restaurantId);
            var json = await resp.json();
            if (!Array.isArray(json) || json.length === 0) {
                 cont.innerHTML = '<div class="restaurant-card">Столики не добавлены.</div>';
                 return;
            }
            cont.innerHTML = '';
            json.forEach(function (t) {
                var el = document.createElement('div');
                el.className = 'restaurant-card';
                el.innerHTML = '<div class="card-row"><div class="card-content"><strong>Стол №' + escapeHtml(String(t.id)) + '</strong><p>Места: ' + escapeHtml(String(t.seats)) + '</p></div><div class="card-actions"><a class="button" href="table_form.php?id=' + t.id + '&restaurant_id=' + t.restaurant_id + '">Редакт.</a><button class="button outline table-delete-btn" data-id="' + t.id + '">Удалить</button></div></div>';
                cont.appendChild(el);
            });
            cont.querySelectorAll('.table-delete-btn').forEach(function(btn) {
                btn.addEventListener('click', async function(e) {
                    var id = e.target.dataset.id;
                    if (!confirm('Удалить этот столик?')) return;
                    var data = new FormData();
                    data.append('id', id);
                    data.append('action', 'delete');
                    try {
                        var delResp = await fetch(apiBase + '?file=table_manage', { method: 'POST', body: data });
                        var delJson = await delResp.json();
                        if (delJson.success) {
                            loadTablesForAdmin(restaurantId);
                        } else {
                            alert(delJson.message || 'Ошибка удаления');
                        }
                    } catch(err) {
                        alert('Бэкенд недоступен');
                    }
                });
            });
        } catch (err) {
            cont.innerHTML = '<div class="restaurant-card placeholder">Ошибка загрузки.</div>';
        }
    }
    
    var tableForm = document.getElementById('table-form');
    if (tableForm) {
        var params = new URLSearchParams(window.location.search);
        var tId = params.get('id');
        var rId = params.get('restaurant_id');
        document.getElementById('t-restaurant-id').value = rId;
        document.getElementById('cancel-table-btn').href = 'tables_list.php?restaurant_id=' + rId;
        if (tId) {
            document.getElementById('form-title').textContent = 'Редактировать столик';
            loadTableForEdit(tId);
        }
        tableForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var data = new FormData(tableForm);
            var msgEl = document.getElementById('form-msg');
            msgEl.textContent = 'Сохранение...';
            try {
                var resp = await fetch(apiBase + '?file=table_manage', {
                    method: 'POST',
                    body: data
                });
                var json = await resp.json();
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Сохранено.';
                    window.location.href = 'tables_list.php?restaurant_id=' + data.get('restaurant_id');
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка сохранения';
                }
            } catch (err) {
                msgEl.style.color = '#a11';
                msgEl.textContent = 'Бэкенд недоступен';
            }
        });
    }

    async function loadTableForEdit(id) {
        try {
            var resp = await fetch(apiBase + '?file=tables&id=' + id);
            var json = await resp.json();
            if (json.id) {
                document.getElementById('t-id').value = json.id;
                document.getElementById('t-seats').value = json.seats;
            } else {
                document.getElementById('form-msg').textContent = json.message || 'Столик не найден';
            }
        } catch (err) {
            document.getElementById('form-msg').textContent = 'Не удалось загрузить данные';
        }
    }
    
    var hoursForm = document.getElementById('hours-form');
    if (hoursForm) {
        var params = new URLSearchParams(window.location.search);
        var rId = params.get('restaurant_id');
        if (!rId) {
            document.getElementById('hours-inputs').innerHTML = 'ID Ресторана не найден';
        } else {
            document.getElementById('r-id').value = rId;
            loadHoursForEdit(rId);
        }
        
        hoursForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var data = new FormData(hoursForm);
            var msgEl = document.getElementById('form-msg');
            msgEl.textContent = 'Сохранение...';
            
            try {
                var resp = await fetch(apiBase + '?file=hours_manage', { method: 'POST', body: data });
                var json = await resp.json();
                
                if (json.success) {
                    msgEl.style.color = 'green';
                    msgEl.textContent = 'Часы сохранены.';
                    window.location.href = 'owner_dashboard.php';
                } else {
                    msgEl.style.color = '#a11';
                    msgEl.textContent = json.message || 'Ошибка сохранения';
                }
            } catch (err) {
                 msgEl.style.color = '#a11';
                 msgEl.textContent = 'Бэкенд недоступен';
            }
        });
    }
    
    async function loadHoursForEdit(rId) {
        var cont = document.getElementById('hours-inputs');
        try {
            var resp = await fetch(apiBase + '?file=restaurants&id=' + rId);
            var json = await resp.json();
            
            if (!json.id) {
                cont.innerHTML = 'Ресторан не найден';
                return;
            }
            
            document.getElementById('r-name').textContent = json.name;
            cont.innerHTML = '';
            
            (json.opening_hours || []).forEach(function(h) {
                var day = dayNames[h.weekday];
                var closed = h.is_closed ? 'checked' : '';
                
                var el = document.createElement('div');
                el.className = 'restaurant-card';
                el.innerHTML = '<h4>' + day + '</h4>'
                    + '<div class="form-row">'
                    + '<label>Открыто <input type="time" name="open[' + h.weekday + ']" value="' + h.open_time + '"></label> '
                    + '<label>Закрыто <input type="time" name="close[' + h.weekday + ']" value="' + h.close_time + '"></label> '
                    + '<label>Выходной <input type="checkbox" name="closed[' + h.weekday + ']" ' + closed + '></label>'
                    + '</div>';
                cont.appendChild(el);
            });
            
        } catch (err) {
            cont.innerHTML = 'Ошибка загрузки часов';
        }
    }


    function escapeHtml(str) {
        if (!str && str !== 0) return '';
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
        });
    }
});