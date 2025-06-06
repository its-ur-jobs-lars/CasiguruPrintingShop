<div>
    {{-- @if(session()->has('message'))
      <div class="bg-green-500 text-black p2 rounded mb-2">
        {{ session('message') }}
     </div>
     @endif --}}

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Add SubCategory</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Add SubCategory</h5>
                    </div>
                        <div class="modal-body">
                        <form wire:submit.prevent="save">
                        

                            {{--  Name --}}


                             <div class="form-group">
                                <label style="color:black;">Category</label>
                                <select wire:model="data.category_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($Category as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subcategory --}}
                            <div class="form-group">
                                <label style="color:black;">Subcategory Name</label>
                                <input type="text" wire:model="data.subcategory_name" class="form-control" required>
                            </div>

                            {{-- Description --}}
                            <div class="form-group">
                                <label style="color:black;">Description</label>
                                <input type="text" wire:model="data.description" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Upload Image</label>
                                <input type="file" wire:model="image" class="form-control" accept="image/*">
                                @error('image') <span class="text-danger">{{ $message }}</span> @enderror

                                @if ($image)
                                    <div class="mt-2 d-flex align-items-center">
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;">
                                        <span class="ml-2 text-success" style="font-size: 17px;">
                                            <i class="fa fa-check-circle"></i> Uploaded
                                        </span>
                                    </div>
                                @endif
                            </div>

                        
                            {{-- Submit Button --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Services</button>
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

</script>

</div>

