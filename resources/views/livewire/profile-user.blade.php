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

                  

                  <div class="col">
                     <label for="employee_id" style="float: left;">Employee ID</label>
                   <input type="text" wire:model="editUser.employee_number" class="form-control" required>
                    </div>
                 </div>

               
                
             
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
           @livewire('changepassword')
            </div>
        </div>
    </div>

        </div>