{{-- Profile Form --}}

    <div class="card-profile p-4 mb-4" style="max-width: 100%;">
        <h4 class="mb-4 text-left">Profile Information</h4>
        <form wire:submit.prevent="save">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

                {{-- Name --}}
                <div class="form-row mb-3" style="display: flex; gap: 10px;">
                    <div class="col">
                        <label for="name" style="float: left;">First Name</label>
                      <input type="text" wire:model="editUser.firstname" class="form-control" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col">
                        <label for="middle_name" style="float: left;">Middle Name</label>
                        <input type="text" wire:model="editUser.middlename" class="form-control" required>
                        @error('middle_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col">
                        <label for="last_name" style="float: left;">Last Name</label>
                        <input type="text" wire:model="editUser.lastname" class="form-control" required>
                        @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                
                    <div class="col">
                        <label for="ext_name" style="float: left;">Extension Name</label>
                        <select class="form-control" id="ext_name" wire:model="editUser.ext_name">
                            <option value="" disabled selected hidden>Select extension name...</option>
                            <option value="0">N/A</option>
                            <option value="1">Jr.</option>
                            <option value="2">Sr.</option>
                            <option value="3">II</option>
                            <option value="4">III</option>
                            <option value="5">IV</option>
                            <option value="6">V</option>
                        </select>
                        @error('ext_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row mb-3" style="display: flex; gap: 10px;">
                    <div class="col">
                          <label for="user_name" style="float: left;">Username</label>
                         <input type="text" wire:model="editUser.username" class="form-control" required>
                        @error('user_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- <div class="col">
                      <label for="division" style="float: left;">Role</label>
                   <select wire:model="editUser.role" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="1">Administrator</option>
                        <option value="2">User</option>
                     </select>
                    @error('division') <span class="text-danger">{{ $message }}</span> @enderror
                    </div> -->

                  <div class="col">
                     <label for="employee_id" style="float: left;">Employee ID</label>
                   <input type="text" wire:model="editUser.employee_number" class="form-control" required>
                    </div>
                 </div>

               
                
                <!-- {{-- Department --}}
                <div class="form-group mb-3">
                    <label for="department" style="float: left;">Department</label>
                    <select class="form-control" id="department" wire:model="department">
                        <option value="" disabled selected hidden>Select department...</option>
                        <option value="1">E-Office of the General Manager</option>
                        <option value="2">E-Finance</option>
                        <option value="3">E-Maintenance</option>
                        <option value="4">E-Operations</option>
                        <option value="5">E-Information Technology</option>
                        <option value="6">E-Human Resource & General Services</option>
                        <option value="7">E-Environment & Health  Safety</option>
                        <option value="8">E-Procurement</option>
                        <option value="9">E-Security</option>
                        <option value="10">E-Audit</option>
                        <option value="11">E-Marine</option>
                        <option value="12">P-Finance</option>
                        <option value="13">P-Maintenance</option>
                        <option value="14">P-Operations</option>
                        <option value="15">P-Environment Health & Safety</option>
                        <option value="16">P-Procurement</option>
                        <option value="17">E-Audit</option>
                        <option value="18">P-Plant & Quarries</option>
                    </select>
                    @error('department') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Position --}}
                <div class="form-group mb-3">
                    <label for="position" style="float: left;">Position</label>
                    <input type="text" class="form-control" id="position" wire:model="position" placeholder="Enter position">
                    @error('position') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Section --}}
                <div class="form-group mb-3">
                    <label for="section" style="float: left;">Section</label>
                    <input type="text" class="form-control" id="section" wire:model="section" placeholder="Enter section">
                    @error('section') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                 -->
                {{-- Submit Button --}}
                <div class="form-group mb-3" style="text-align: right;">
                    <button type="submit" class="btn btn-primary">Save</button>
                    
                </div>
                                
            </form>
        </div>

            <div class="card-profile p-4 mb-4" style="max-width: 100%;">
            <h4 class="mb-4 text-left">Security</h4>

            <div class="form-group mb-3 d-flex align-items-center" style="gap: 16px;">
            <label for="change_password" class="mb-0" style="min-width: 140px;">Change Password</label>
            <label style="color: var(--gray); font-style: italic; margin-right: 16px;">
                Current User: {{ Auth::user()->username }}
            </label>
            <label style="color: var(--gray); font-style: italic;">
                Last Password Change: {{ $lastPasswordChange ? $lastPasswordChange->format('Y-m-d H:i') : 'Unknown' }}
            </label>
       <button wire:click="openChangePasswordModal"
        class="bg-red-500 text-white px-3 py-1 rounded-2">
        <i class="fas fa-solid fa-key"></i>
      </button>
        </div>
    </div>


    @if($isChangePasswordModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" wire:click="closeChangePasswordModal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="password" wire:model.defer="passwordData.new_password" class="form-control mb-2" placeholder="New Password">
                    <input type="password" wire:model.defer="passwordData.confirm_password" class="form-control mb-2" placeholder="Confirm Password">
                    @error('passwordData.new_password') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('passwordData.confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeChangePasswordModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="changePassword">Save</button>
                </div>
            </div>
        </div>
    </div>
@endif

        </div>