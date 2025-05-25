<div>
    {{-- <h2 class="text-lg font-semibold mb-4">Laptop Inventory</h2> --}}

    
        <!-- Search Filter -->
        <input type="text" wire:model.debounce.500ms="search" placeholder="Search..."
            class="border border-gray-300 rounded-9 px-3 py-2 w-full">
    
          
        <select wire:model="filterActivation" class="border border-gray-300 rounded px-3 py-2 w-full mb-4">
            <option value="1">Active</option>
            <option value="0">Inactive</option>   
        </select>
               
            <div class="overflow-x-auto-1 bg-white shadow-md rounded-lg relative">
                <div class="overflow-y-auto max-h-[500px]">
                    <table class="min-w-full border-collapse">
                        <thead class="sticky top-0 bg-gray-100 z-10">
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Last Name</th>
                                <th class="px-4 py-2">First Name</th>
                                <th class="px-4 py-2">Middle Name</th>
                                <th class="px-4 py-2">Extension Name</th>
                                <th class="px-4 py-2">Username</th>
                                <th class="px-4 py-2">Employee Number</th>
                                <th class="px-4 py-2">Role</th>
                                <th class="px-4 py-2">Updated At</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->lastname }}</td>

                                    <td class="px-4 py-2">{{ $record->firstname }}</td>

                                    <td class="px-4 py-2">{{ $record->middlename }}</td>

                                    <td class="px-4 py-2">{{ $record->ext_name }}</td>

                                    <td class="px-4 py-2">{{ $record->username }}</td>

                                    <td class="px-4 py-2">{{ $record->employee_number }}</td>
                                
                            
                                    <td class="px-4 py-2">
                                    @php 
                                    $role = [
                                        1 => 'Administrator',
                                        2 => 'User'
                                    ]
                                    @endphp
                                    {{ $role[$record->role] ?? 'Not Applicable' }}
                                </td>
                                    <td class="px-4 py-2">{{ $record->updated_at ?? 'Unknown' }}</td>
                                
                                <td class="px-4 py-2">
                                    @if ($record->isActive)
                                        <span class="text-green-600 font-semibold">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @else
                                        <span class="text-red-600 font-semibold">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                    @endif
                                </td>  
                        <td class="px-4 py-2">
                            <div class="button-column">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $record->id }})"
                                    class="bg-blue-500 text-white px-3 py-1 rounded-1">
                                    <i class="fas fa-solid fa-pen-to-square"></i></button>

                                        <!-- Delete Button -->
                                        <button wire:click="openChangePasswordModal({{ $record->id }})"
                                            class="bg-red-500 text-white px-3 py-1 rounded-2">
                                            <i class="fas fa-solid fa-key"></i></button>
                                    </div>
                                </td>    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            

<!-- Fixed Footer for Row Count -->
<div class="fixed bottom-0 left-0 w-full p-2 z-20">
    <span class="text-sm text-gray-600">Total number of Users : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Personal Information</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            @if ($step === 1)

                            {{-- Last Name --}}
                            <div class="form-group">
                                <label style="color:black;">Last Name</label>
                                <input type="text" wire:model="editUser.lastname" class="form-control" required>
                            </div>

                            {{-- First Name --}}
                            <div class="form-group">
                                <label style="color:black;">First Name</label>
                                <input type="text" wire:model="editUser.firstname" class="form-control" required>
                            </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Middle Name</label>
                                <input type="text" wire:model="editUser.middlename" class="form-control" required>
                            </div>

                            {{-- Extension Name --}}
                            <div class="form-group">
                                <label style="color:black;">Extension Name</label>
                                <input type="text" wire:model="editUser.ext_name" class="form-control" required>
                            </div>

                            {{-- Username --}}
                            <div class="form-group">
                                <label style="color:black;">Username</label>
                                <input type="text" wire:model="editUser.username" class="form-control" required>
                            </div>

                            {{-- Next Button--}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                            </div>
                            
                            @endif

                            <!-- Page 2 -->
                            @if ($step === 2)

                          

                            {{-- Employee Number --}}
                            <div class="form-group">
                                <label style="color:black;">Employee Number</label>
                                <input type="text" wire:model="editUser.employee_number" class="form-control" required>
                            </div>
                            
                         
                           {{-- Role --}}
                            <div class="form-group">
                                <label style="color:black;">Role</label>
                               <select wire:model="editUser.role" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="1">Administrator</option>
                                    <option value="2">User</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Status</label>
                               <select wire:model="editUser.isActive" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            {{-- Submit Button --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                               {{-- <button wire:click="update()" class="bg-green-500 text-white px-3 py-1 rounded-3 mt-2">Apply changes</button> --}}
                               <button type="button"  class="btn btn-success"  wire:click="update()">Apply changes</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

        

 {{-- Edit Function
 @if($confirmDelete)
 <div class="modal-backdrop-1 show"></div>
 <div class="modal fade show d-block" tabindex="-1">
     <div class="modal-dialog-1 modal-lg">
         <div class="modal-content">
            <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white-1 p-6 rounded shadow-lg w-1/3">
                    <h2 class="text-lg font-semibold mb-4">Confirm Delete</h2>
                    <p>Are you sure you want to delete this product?</p>
            
                    <div class="flex justify-end space-x-4 mt-4">      
                       <button wire:click="$set('confirmDelete', null)" class="btn btn-success">Cancel</button>
                       <button wire:click="delete()" class="btn btn-secondary">Delete</button>
                    </div>

                </div>
            </div>
         </div>
     </div>
 </div>
@endif --}}

@if($isChangePasswordModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" wire:click="closeChangePasswordModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="changePassword">
                        {{-- New Password --}}
                        <div class="form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" wire:model="passwordData.new_password" class="form-control" required>
                            @error('passwordData.new_password') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-group">
                            <label for="confirm-password">Confirm Password</label>
                            <input type="password" id="confirm-password" wire:model="passwordData.confirm_password" class="form-control" required>
                            @error('passwordData.confirm_password') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Custom error message from backend --}}
                        @if (session()->has('error'))
                            <div class="alert alert-danger text-center mt-2">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group text-center">
                            <button type="button" class="btn btn-secondary" wire:click="closeChangePasswordModal">Cancel</button>
                            <button type="submit" class="btn btn-secondary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif


@if (session()->has('message') || session()->has('error'))
    <div class="fixed-3 inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3 text-center m-4">
            @if (session()->has('message'))
                <div class="text-green-600 font-semibold text-lg">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-600 font-semibold text-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded-8" onclick="closeAlertDelete()">OK</button> --}}
        </div>
    </div>
@endif




@if (session()->has('messageUpdate') || session()->has('error'))
    <div class="fixed-3 inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3 text-center m-4">
            @if (session()->has('messageUpdate'))
                <div class="text-green-600 font-semibold text-lg">
                    {{ session('messageUpdate') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="text-red-600 font-semibold text-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded-8" onclick="closeAlertDelete()">OK</button> --}}
        </div>
    </div>
@endif


<script>
    function closeAlertDelete() {
        document.querySelector('.fixed.inset-0').style.display = 'none';
    }
</script>

</div> 