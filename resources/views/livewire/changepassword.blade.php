<div>
    {{-- @if(session()->has('message'))
      <div class="bg-green-500 text-black p2 rounded mb-2">
        {{ session('message') }}
     </div>
     @endif --}}

    {{-- Button to Open Modal --}}
    <button wire:click="openChangePasswordModal" class="bg-red-500 text-white px-3 py-1 rounded-2">
    <i class="fas fa-solid fa-key"></i>
     </button>

    {{-- Product Form Modal --}}
   @if($isChangePasswordModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" wire:click="closeChangePasswordModal">&times;</button>
                </div>
                <div class="modal-body text-left">
                    <div class="mb-2" style="text-align: left;">
                        <strong>Username:</strong> {{ $modalUserName }} 
                    </div>
                    <div class="position-relative mb-2">
                        <input id="current_password" type="password" wire:model.defer="passwordData.current_password" class="form-control" placeholder="Current Password" style="text-align: left; padding-right: 40px;">
                        <span onclick="togglePasswordVisibility('current_password')" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
                            <i id="current_password-icon" class="fa fa-eye"></i>
                        </span>
                    </div>
                    @error('passwordData.current_password') <span class="text-danger">{{ $message }}</span> @enderror

                    <div class="position-relative mb-2">
                        <input id="new_password" type="password" wire:model.defer="passwordData.new_password" class="form-control" placeholder="New Password" style="text-align: left; padding-right: 40px;">
                        <span onclick="togglePasswordVisibility('new_password')" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
                            <i id="new_password-icon" class="fa fa-eye"></i>
                        </span>
                    </div>
                    @error('passwordData.new_password') <span class="text-danger">{{ $message }}</span> @enderror

                    <div class="position-relative mb-2">
                        <input id="confirm_password" type="password" wire:model.defer="passwordData.confirm_password" class="form-control" placeholder="Confirm Password" style="text-align: left; padding-right: 40px;">
                        <span onclick="togglePasswordVisibility('confirm_password')" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
                            <i id="confirm_password-icon" class="fa fa-eye"></i>
                        </span>
                    </div>
                    @error('passwordData.confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-primary mx-2" wire:click="closeChangePasswordModal">Cancel</button>
                <button type="button" class="btn btn-primary mx-2" wire:click="changePassword">Save</button>
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

