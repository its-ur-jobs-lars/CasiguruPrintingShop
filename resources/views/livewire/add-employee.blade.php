<div>
    {{-- @if(session()->has('message'))
      <div class="bg-green-500 text-black p2 rounded mb-2">
        {{ session('message') }}
     </div>
     @endif --}}

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Add Employee</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
         <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog-1 modal-lg">
        <div class="card-profile p-4 mb-4" style="max-width: 100%;">
        <h4 class="mb-4 text-left">Employee Information</h4>
        <form wire:submit.prevent="save">

                {{-- Name --}}
                <div class="form-row mb-3" style="display: flex; gap: 10px;">

                    <div class="col">
                        <label for="name" style="float: left;">First Name</label>
                      <input type="text" wire:model="data.emp_Firstname" class="form-control" required>
                      @error('data.emp_Firstname') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col">
                        <label for="middle_name" style="float: left;">Middle Name</label>
                        <input type="text" wire:model="data.emp_Middlename" class="form-control" required>
                       @error('data.emp_Middlename') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col">
                        <label for="last_name" style="float: left;">Last Name</label>
                        <input type="text" wire:model="data.emp_Lastname" class="form-control" required>
                        @error('data.emp_Lastname') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                
                    <div class="col">
                        <label for="ext_name" style="float: left;">Extension Name</label>
                        <select class="form-control" id="ext_name" wire:model="data.ext_name">
                            <option value="">Select extension name...</option>
                            <option value="0">N/A</option>
                            <option value="1">Jr.</option>
                            <option value="2">Sr.</option>
                            <option value="3">II</option>
                            <option value="4">III</option>
                            <option value="5">IV</option>
                            <option value="6">V</option>
                        </select>
                        @error('data.ext_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row mb-3" style="display: flex; gap: 10px;">
                    <div class="col">
                          <label for="task" style="float: left;">Employee Number</label>
                         <input type="text" wire:model="data.employee_number" class="form-control" required>
                      @error('data.employee_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                 <div class="form-row mb-3" style="display: flex; gap: 10px;">
                    <div class="col">
                          <label for="date_hired" style="float: left;">Date Hired</label>
                         <input type="date" wire:model="data.date_Hired" class="form-control" required>
                     @error('data.date_Hired') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                  

                  <div class="col">
                     <label for="description" style="float: left;">Task</label>
                   <input type="text" wire:model="data.task" class="form-control" required>
                   @error('data.description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                 </div>
               </div>

                <div class="form-row mb-3" style="display: flex; gap: 10px;">
                  <!-- <div class="col">
                     <label for="employee_number" style="float: left;">Employee Number</label>
                   <input type="text" wire:model="data.employee_number" class="form-control" required>
                   @error('data.employee_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div> -->

                    <div class="col">
                          <label for="task" style="float: left;">Job Description</label>
                         <input type="text" wire:model="data.description" class="form-control" required>
                       @error('data.description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col">
                          <label for="task" style="float: left;">Job Description</label>
                         <input type="text" wire:model="data.description" class="form-control" required>
                       @error('data.description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
              </div>

                {{-- Submit Button --}}
                <div class="form-group mb-3" style="text-align: right;">
                    <button type="submit" class="btn btn-primary">Save</button>
                    
                </div>
                                
            </form>
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

