<div>
    {{-- The best athlete wants his opponent at his best. --}}
</div>
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
                                <th class="px-4 py-2">SubCategory ID</th>
                                <th class="px-4 py-2">Category ID</th>
                                <th class="px-4 py-2">SubCategory Name</th>
                                <th class="px-4 py-2">Description</th>
                                <!-- <th class="px-4 py-2">Image</th> -->
                                <th class="px-4 py-2">Updated By</th>
                                <th class="px-4 py-2">Updated At</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->subcategory_id }}</td>

                                 <td class="px-4 py-2">{{ $Category[$record->category_id] ?? 'Not Available' }}</td>
                                    <td class="px-4 py-2">{{ $record->subcategory_name }}</td>

                                    <td class="px-4 py-2">{{ $record->description }}</td>

                                    <!-- <td class="px-4 py-2">
                                        @if ($record->image)
                                            <img src="{{ asset('storage/' . $record->image) }}" alt="Category Image"
                                                class="w-16 h-16 object-cover">
                                        @else
                                            <span class="text-gray-500">No Image</span>
                                        @endif
                                    </td> -->

                                    <td class="px-4 py-2">{{ $record->updated_by ?? 'New Added' }}</td>
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
    <span class="text-sm text-gray-600">Total number of SubCategory : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
       @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Category</h5>
                    </div>
                    <div class="modal-body">
                        <form>

                            {{-- Category Name --}}
                            <div class="form-group">
                                <label style="color:black;">Category Name</label>
                                <input type="text" wire:model="editCategory.subcategory_name" class="form-control" required>
                            </div>

                           

                            {{-- Description --}}
                            <div class="form-group">
                                <label style="color:black;">Description</label>
                                <input type="text" wire:model="editCategory.description" class="form-control" required>
                            </div>
                            
                            {{-- Image --}}     
                            <div class="form-group">
                                <label style="color:black;">Image</label>
                                <input type="file" wire:model="editCategory.image" class="form-control-file">
                                @if ($editCategory->image)
                                    <img src="{{ asset('storage/' . $editCategory->image) }}" alt="Category Image"
                                        class="w-16 h-16 object-cover mt-2">
                                @endif
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Status</label>
                               <select wire:model="editCategory.isActive" class="form-control" required>
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