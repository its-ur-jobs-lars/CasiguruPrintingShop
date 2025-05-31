<div>
    {{-- @if(session()->has('message'))
      <div class="bg-green-500 text-black p2 rounded mb-2">
        {{ session('message') }}
     </div>
     @endif --}}

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Add PriceList</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Add Prcelist</h5>
                    </div>
                        <div class="modal-body">
                        <form wire:submit.prevent="save">
                        

                             <div class="form-group">
                                <label style="color:black;">Category</label>
                                <select wire:model="data.category_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($Category as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="form-group">
                                <label style="color:black;">SubCategory</label>
                                <select wire:model="data.subcategory_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($SubCategory as $subcategory)
                                        <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Price (10 to 50 pcs)</label>
                                <input type="text" wire:model="data.price_10_50" class="form-control" required>
                            </div>


                            <div class="form-group">
                                <label style="color:black;">Price (51 to 100 pcs)</label>
                                <input type="text" wire:model="data.price_51_100" class="form-control" required>
                            </div>

                           <div class="form-group">
                                <label style="color:black;">Price (101 to 500 pcs)</label>
                                <input type="text" wire:model="data.price_101_500" class="form-control" required>
                            </div>

                              <div class="form-group">
                                <label style="color:black;">Remarks</label>
                                <input type="text" wire:model="data.remarks" class="form-control" required>
                            </div>
                        
                        
                            {{-- Submit Button --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Pricelist</button>
                            </div>
                        </form>
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

