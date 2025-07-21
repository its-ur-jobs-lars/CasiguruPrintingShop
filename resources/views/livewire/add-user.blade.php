<div>
    {{-- @if(session()->has('message'))
      <div class="bg-green-500 text-black p2 rounded mb-2">
        {{ session('message') }}
     </div>
     @endif --}}

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Add User</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Add User</h5>
                    </div>
                        <div class="modal-body">
                        <form wire:submit.prevent="save">
                            {{-- Product ID --}}
                            @if ($step === 1)

                            {{-- Last Name --}}
                            <div class="form-group">
                                <label style="color:black;">Last Name</label>
                                <input type="text" wire:model="data.lastname" class="form-control" required>
                            </div>

                            {{-- First Name --}}
                            <div class="form-group">
                                <label style="color:black;">First Name</label>
                                <input type="text" wire:model="data.firstname" class="form-control" required>
                            </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Middle Name</label>
                                <input type="text" wire:model="data.middlename" class="form-control" required>
                            </div>

                            {{-- Extension Name --}}
                            <div class="form-group">
                                <label style="color:black;">Extension Name</label>
                               <select wire:model="data.ext_name" class="form-control" required>
                                 <option value="">Select extension name...</option>
                                <option value="0">N/A</option>
                                <option value="1">Jr.</option>
                                <option value="2">Sr.</option>
                                <option value="3">II</option>
                                <option value="4">III</option>
                                <option value="5">IV</option>
                                <option value="6">V</option>
                             </select>
                            </div>

                            {{-- Username --}}
                            <div class="form-group">
                                <label style="color:black;">Username</label>
                                <input type="text" wire:model="data.username" class="form-control" required>
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
                                <input type="text" wire:model="data.employee_number" class="form-control" required>
                            </div> 
                            

                            {{-- Role --}}
                            <div class="form-group">
                                <label style="color:black;">Role</label>
                            <select wire:model="data.role" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="Graphic Artist">Graphic Artist</option>
                                    <option value="Secretary">Secretary</option>
                                    <option value="User">User</option>
                                    <option value="Production">Production</option>
                                </select>
                            </div>


                            {{-- Password --}}
                            <div class="form-group">
                                <label style="color:black;">Password</label>
                                <div class="input-group">
                                    <input type="{{ $showPassword ? 'text' : 'password' }}" id="password" wire:model="data.password" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" wire:click="$toggle('showPassword')" style="cursor: pointer;">
                                            <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group">
                                <label style="color:black;">Confirm Password</label>
                                <div class="input-group">
                                    <input type="{{ $showPassword ? 'text' : 'password' }}" id="confirm-password" wire:model="data.confirm_password" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" wire:click="$toggle('showPassword')" style="cursor: pointer;">
                                            <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- Submit Button --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                <button type="submit" class="btn btn-success">Save Account User</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>


      
        @endif

    @if (session()->has('messageInsert') || session()->has('errorInsert'))
    <div class="fixed-3 inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3 text-center m-4">
            @if (session()->has('messageInsert'))
                <div class="text-green-600 font-semibold text-lg">
                    {{ session('messageInsert') }}
                </div>
            @endif

            @if (session()->has('errorInsert'))
                <div class="text-red-600 font-semibold text-lg">
                    {{ session('errorInsert') }}
                </div>
            @endif

            {{-- <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-8" onclick="closeAlertInsert()">OK</button> --}}
        </div>
    </div>
@endif

<script>
    function closeAlertInsert() {
        document.querySelector('.fixed.inset-0').style.display = 'none';
    }

    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.getElementById(`${fieldId}-icon`);
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

@livewireScripts

</div>

