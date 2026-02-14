@extends('layouts.app')

@section('title', 'CRM Contacts')

@section('content')
<div x-data="contactManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Contacts</h1>
            <p class="text-gray-500 dark:text-gray-400">Manage your CRM contacts</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('crm.index') }}" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-chart-pie mr-2"></i> Dashboard
            </a>
            <button @click="openCreateModal()" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Contact
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('crm.contacts') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..."
                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
            </div>
            <select name="type" class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                <option value="">All Types</option>
                <option value="lead" {{ request('type') === 'lead' ? 'selected' : '' }}>Lead</option>
                <option value="prospect" {{ request('type') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                <option value="customer" {{ request('type') === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="partner" {{ request('type') === 'partner' ? 'selected' : '' }}>Partner</option>
            </select>
            <select name="status" class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
        </form>
    </div>

    <!-- Contacts List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deals</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tasks</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary-600 font-semibold text-sm">{{ strtoupper(substr($contact->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $contact->name }}</p>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                            @if($contact->email)
                                                <span><i class="fas fa-envelope mr-1"></i>{{ $contact->email }}</span>
                                            @endif
                                            @if($contact->phone)
                                                <span><i class="fas fa-phone mr-1"></i>{{ $contact->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $contact->company_name ?? '—' }}
                                @if($contact->designation)
                                    <br><span class="text-xs text-gray-400">{{ $contact->designation }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    @if($contact->type === 'customer') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($contact->type === 'lead') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($contact->type === 'prospect') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @else bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                    @endif">
                                    {{ ucfirst($contact->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    {{ $contact->status === 'active'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst($contact->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $contact->deals_count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $contact->tasks_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="openViewModal({{ $contact->toJson() }})" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="openEditModal({{ $contact->toJson() }})" class="p-2 text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('crm.contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-address-book text-4xl mb-3"></i>
                                <p class="text-lg">No contacts found</p>
                                <p class="text-sm mt-1">Click "Add Contact" to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contacts->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $contacts->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white" x-text="editMode ? 'Edit Contact' : 'New Contact'"></h3>
                    <button @click="showModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <form :action="editMode ? '/crm/contacts/' + form.id : '{{ route('crm.contacts.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="text" name="phone" x-model="form.phone" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                                <input type="text" name="company_name" x-model="form.company_name" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Designation</label>
                                <input type="text" name="designation" x-model="form.designation" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type <span class="text-red-500">*</span></label>
                                <select name="type" x-model="form.type" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="lead">Lead</option>
                                    <option value="prospect">Prospect</option>
                                    <option value="customer">Customer</option>
                                    <option value="partner">Partner</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source</label>
                                <input type="text" name="source" x-model="form.source" placeholder="e.g., Website, Referral" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select name="status" x-model="form.status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <textarea name="address" x-model="form.address" rows="2" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" x-model="form.notes" rows="2" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-3">
                        <button type="button" @click="showModal = false" class="px-6 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors" x-text="editMode ? 'Update Contact' : 'Create Contact'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50" @click="showViewModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Contact Details</h3>
                    <button @click="showViewModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Contact Info -->
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <span class="text-primary-600 font-bold text-xl" x-text="viewContact.name ? viewContact.name.substring(0,2).toUpperCase() : ''"></span>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 dark:text-white" x-text="viewContact.name"></h4>
                            <p class="text-gray-500 dark:text-gray-400">
                                <span x-text="viewContact.designation || ''"></span>
                                <template x-if="viewContact.designation && viewContact.company_name"> at </template>
                                <span x-text="viewContact.company_name || ''"></span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Email</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="viewContact.email || '—'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="viewContact.phone || '—'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Type</p>
                            <p class="font-medium text-gray-800 dark:text-white capitalize" x-text="viewContact.type"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Source</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="viewContact.source || '—'"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500 dark:text-gray-400">Address</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="viewContact.address || '—'"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500 dark:text-gray-400">Notes</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="viewContact.notes || '—'"></p>
                        </div>
                    </div>

                    <!-- Quick Add Note -->
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"><i class="fas fa-sticky-note mr-1"></i> Quick Add Note</h5>
                        <form :action="'/crm/contacts/' + viewContact.id + '/notes'" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="content" placeholder="Add a note..." required class="flex-1 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Quick Add Task -->
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"><i class="fas fa-tasks mr-1"></i> Quick Add Task</h5>
                        <form :action="'/crm/contacts/' + viewContact.id + '/tasks'" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="title" placeholder="Task title..." required class="flex-1 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <input type="date" name="due_date" class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function contactManager() {
    return {
        showModal: false,
        showViewModal: false,
        editMode: false,
        form: { id: '', name: '', email: '', phone: '', company_name: '', designation: '', address: '', type: 'lead', source: '', status: 'active', notes: '' },
        viewContact: {},
        openCreateModal() {
            this.editMode = false;
            this.form = { id: '', name: '', email: '', phone: '', company_name: '', designation: '', address: '', type: 'lead', source: '', status: 'active', notes: '' };
            this.showModal = true;
        },
        openEditModal(contact) {
            this.editMode = true;
            this.form = { ...contact };
            this.showModal = true;
        },
        openViewModal(contact) {
            this.viewContact = contact;
            this.showViewModal = true;
        },
    };
}
</script>
@endpush
@endsection
