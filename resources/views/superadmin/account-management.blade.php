@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 pt-0">
    <div 
        x-data="{ showNotification: false, message: '', type: 'error' }"
        @notify.window="message = $event.detail.message; type = $event.detail.type || 'error'; showNotification = true; setTimeout(() => showNotification = false, 3000)"
        class="relative"
    >
        <!-- Notification Toast -->
        <div
            x-show="showNotification"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'"
            class="fixed top-20 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-50"
        >
            <div class="flex items-center space-x-2">
                <template x-if="type === 'success'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </template>
                <span x-text="message"></span>
            </div>
        </div>
        <div class="bg-black/30 backdrop-blur-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Account Management</h1>
            
            <!-- Account Search and Filters -->
            <div class="mb-6 flex gap-4">
                <div class="flex-1">
                    <input type="text" placeholder="Search accounts..." class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg focus:outline-none focus:border-[#5fbbd1] text-white" oninput="filterTable()">
                </div>
                <div>
                    <select class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg focus:outline-none focus:border-[#5fbbd1] text-white" onchange="filterByRole(this)">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <button class="px-6 py-2 bg-[#5fbbd1] hover:bg-[#4da9bf] rounded-lg text-white transition-colors" onclick="openAddAccountModal()">
                    Add New Account
                </button>
            </div>

            <!-- Accounts Table -->
            <div class="overflow-x-auto">
                <table class="w-full" id="accountsTable">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 text-left">School ID</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="bg-white/5 hover:bg-white/10 transition-colors" data-user-id="{{ $user->id }}">
                            <td class="px-4 py-3 user-school-id">
                                <span>{{ $user->school_id }}</span>
                                <input type="text" class="w-full px-4 py-2 bg-transparent border border-gray-300 rounded-lg hidden" value="{{ $user->school_id }}">
                            </td>
                            <td class="px-4 py-3 user-name">
                                <span>{{ $user->name }}</span>
                                <input type="text" class="w-full px-4 py-2 bg-transparent border border-gray-300 rounded-lg hidden" value="{{ $user->name }}">
                            </td>
                            <td class="px-4 py-3 user-email">
                                <span>{{ $user->email }}</span>
                                <input type="email" class="w-full px-4 py-2 bg-transparent border border-gray-300 rounded-lg hidden" value="{{ $user->email }}">
                            </td>
                            <td class="px-4 py-3">{{ ucfirst($user->role) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-green-500/20 text-green-500 rounded-full text-sm">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button class="p-1 hover:text-[#5fbbd1] edit-button" onclick="editUser('{{ $user->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button class="p-1 hover:text-red-500" onclick="deleteUser('{{ $user->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <button class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 hidden" onclick="saveUser('{{ $user->id }}')">Save</button>
                                    <button class="px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 hidden" onclick="cancelEdit('{{ $user->id }}')">Cancel</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="border-b border-white/10">
                            <td colspan="6" class="px-4 py-3 text-center">No accounts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div id="addAccountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Add New Account</h2>
            <form id="addAccountForm">
                <div class="mb-4">
                    <label class="block text-gray-700">Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Role</label>
                    <select name="role" class="w-full px-4 py-2 border rounded-lg">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2" onclick="closeAddAccountModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#5fbbd1] text-white rounded-lg">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Edit User</h2>
            <form id="editUserForm">
                <input type="hidden" id="editUserId">
                <div class="mb-4">
                    <label for="editUserName" class="block text-sm font-medium">Name</label>
                    <input type="text" id="editUserName" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="editUserEmail" class="block text-sm font-medium">Email</label>
                    <input type="email" id="editUserEmail" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditUserModal()" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Save</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        select {
            background-color: transparent;
            color: white;
        }
        select option {
            background-color: #1a1a1a; /* Dark background to match theme */
            color: white; /* White text for contrast */
            padding: 8px;
        }
        select option:hover {
            background-color: #333; /* Slightly lighter on hover */
        }
    </style>

    <script>
        function editUser(userId) {
            const userRow = document.querySelector(`tr[data-user-id='${userId}']`);
            userRow.querySelectorAll('span').forEach(span => span.classList.add('hidden'));
            userRow.querySelectorAll('input').forEach(input => {
                input.classList.remove('hidden');
                input.setAttribute('data-original-value', input.value);
            });
            userRow.querySelectorAll('.bg-green-500, .bg-gray-500').forEach(button => button.classList.remove('hidden'));
            userRow.querySelector('.edit-button').classList.add('hidden');
        }

        function cancelEdit(userId) {
            const userRow = document.querySelector(`tr[data-user-id='${userId}']`);
            userRow.querySelectorAll('span').forEach(span => span.classList.remove('hidden'));
            userRow.querySelectorAll('input').forEach(input => {
                input.classList.add('hidden');
                input.value = input.getAttribute('data-original-value');
            });
            userRow.querySelectorAll('.bg-green-500, .bg-gray-500').forEach(button => button.classList.add('hidden'));
            userRow.querySelector('.edit-button').classList.remove('hidden');
        }

        function saveUser(userId) {
            const userRow = document.querySelector(`tr[data-user-id='${userId}']`);
            const userName = userRow.querySelector('.user-name input').value;
            const userEmail = userRow.querySelector('.user-email input').value;
            const userSchoolId = userRow.querySelector('.user-school-id input').value;

            fetch('/superadmin/account/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId,
                    name: userName,
                    email: userEmail,
                    school_id: userSchoolId
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    userRow.querySelector('.user-name span').textContent = userName;
                    userRow.querySelector('.user-email span').textContent = userEmail;
                    userRow.querySelector('.user-school-id span').textContent = userSchoolId;
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'User updated successfully', type: 'success' }
                    }));
                    cancelEdit(userId);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Error: All fields are required.', type: 'error' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'An error occurred while updating the user', type: 'error' }
                }));
            });
        }

        function filterTable() {
            const input = document.querySelector('input[placeholder="Search accounts..."]');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('accountsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
                const tdName = tr[i].getElementsByClassName('user-name')[0];
                const tdEmail = tr[i].getElementsByClassName('user-email')[0];
                const tdSchoolID = tr[i].getElementsByClassName('user-school-id')[0]; // Assuming School ID is the first column

                if (tdName || tdEmail || tdSchoolID) {
                    const nameValue = tdName.textContent || tdName.innerText;
                    const emailValue = tdEmail.textContent || tdEmail.innerText;
                    const schoolIDValue = tdSchoolID.textContent || tdSchoolID.innerText;

                    if (nameValue.toLowerCase().indexOf(filter) > -1 ||
                        emailValue.toLowerCase().indexOf(filter) > -1 ||
                        schoolIDValue.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        function filterByRole(selectElement) {
            const selectedRole = selectElement.value;
            const table = document.getElementById('accountsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
                const tdRole = tr[i].getElementsByTagName('td')[3]; // Assuming Role is the fourth column

                if (tdRole) {
                    const roleValue = tdRole.textContent || tdRole.innerText;

                    if (selectedRole === "" || roleValue.toLowerCase() === selectedRole.toLowerCase()) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function openAddAccountModal() {
            const table = document.getElementById('accountsTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();

            newRow.innerHTML = `
                <td class="px-4 py-3"><input type="text" class="w-full bg-transparent border border-gray-300 rounded-lg" placeholder="School ID"></td>
                <td class="px-4 py-3"><input type="text" class="w-full bg-transparent border border-gray-300 rounded-lg" placeholder="Name"></td>
                <td class="px-4 py-3"><input type="email" class="w-full bg-transparent border border-gray-300 rounded-lg" placeholder="Email"></td>
                <td class="px-4 py-3">
                    <select class="w-full bg-transparent border border-gray-300 rounded-lg">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 bg-green-500/20 text-green-500 rounded-full text-sm">Active</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <button class="px-2 py-1 bg-[#5fbbd1] text-white rounded-lg" onclick="saveNewAccount(this)">Save</button>
                        <button class="px-2 py-1 bg-gray-500 text-white rounded-lg" onclick="cancelNewAccount(this)">Cancel</button>
                    </div>
                </td>
            `;
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function saveNewAccount(button) {
            const row = button.parentElement.parentElement.parentElement;
            const inputs = row.getElementsByTagName('input');
            const select = row.getElementsByTagName('select')[0];

            const email = inputs[2].value;
            if (!validateEmail(email)) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Please enter a valid email address', type: 'error' }
                }));
                return;
            }

            const accountData = {
                school_id: inputs[0].value,
                name: inputs[1].value,
                email: email,
                role: select.value
            };

            // Send AJAX request to save account
            fetch('/superadmin/account/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(accountData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update row with input values
                    row.innerHTML = `
                        <td class="px-4 py-3">${accountData.school_id}</td>
                        <td class="px-4 py-3">${accountData.name}</td>
                        <td class="px-4 py-3">${accountData.email}</td>
                        <td class="px-4 py-3">${accountData.role.charAt(0).toUpperCase() + accountData.role.slice(1)}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-500/20 text-green-500 rounded-full text-sm">Active</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button class="p-1 hover:text-[#5fbbd1] edit-button" onclick="editUser('${accountData.school_id}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    `;
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Account added successfully', type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Error: All fields are required.', type: 'error' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'An error occurred while adding the account', type: 'error' }
                }));
            });
        }

        function cancelNewAccount(button) {
            const row = button.parentElement.parentElement.parentElement;
            row.remove();
        }

        function closeAddAccountModal() {
            document.getElementById('addAccountModal').classList.add('hidden');
        }

        document.getElementById('addAccountForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Add account creation logic here
            closeAddAccountModal();
        });

        function deleteUser(userId) {
            // Confirm before deletion
            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }

            // Send AJAX request to delete user
            fetch(`/superadmin/account/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    const userRow = document.querySelector(`tr[data-user-id='${userId}']`);
                    if (userRow) {
                        userRow.remove();
                    }

                    // Dispatch notification event
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            message: data.message,
                            type: 'success'
                        }
                    }));
                } else {
                    // Dispatch error notification
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            message: data.message,
                            type: 'error'
                        }
                    }));
                }
            })
            .catch(error => {
                // Dispatch error notification
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'An error occurred while deleting the user',
                        type: 'error'
                    }
                }));
            });
        }
    </script>

@endsection