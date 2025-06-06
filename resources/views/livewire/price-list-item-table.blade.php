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
                                <th class="px-4 py-2">Pricelist ID</th>
                                <th class="px-4 py-2">Category Name</th>
                                <th class="px-4 py-2">SubCategory Name</th>
                                <th class="px-4 py-2">Price (1 pcs)</th>
                                <th class="px-4 py-2">Price ( 2 to 50 pcs)</th>
                                <th class="px-4 py-2">Price (51 to 100 pcs)</th>
                                <th class="px-4 py-2">Price ( 101 to 500 pcs)</th>
                                <th class="px-4 py-2">Price (501 to 999 pcs)</th>
                                <th class="px-4 py-2">Price (1000+  pcs)</th>
                                <th class="px-4 py-2">Remarks</th>
                                <th class="px-4 py-2">Added_by</th>
                                 <th class="px-4 py-2">Updated By</th>
                                  <th class="px-4 py-2">Created At</th>
                                <th class="px-4 py-2">Updated At</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->pricelist_id }}</td>

                                      <td class="px-4 py-2">{{ $Category[$record->category_id] ?? 'Not Available' }}</td>

                                      <td class="px-4 py-2">{{ $SubCategory[$record->subcategory_id] ?? 'Not Available' }}</td>

                                      <td class="px-4 py-2">{{ $record->price_1 }}</td>
                                    

                                    <td class="px-4 py-2">{{ $record->price_2_50 }}</td>

                                    <td class="px-4 py-2">{{ $record->price_51_100 }}</td>

                                      <td class="px-4 py-2">{{ $record->price_101_500 }}</td>
                                    

                                    <td class="px-4 py-2">{{ $record->price_501_999}}</td>

                                    <td class="px-4 py-2">{{ $record->price_1000_up }}</td>
                                
                                   <td class="px-4 py-2">{{ $record->remarks }}</td>

                                    <td class="px-4 py-2">{{ $record->added_by }}</td>

                                     <td class="px-4 py-2">{{ $record->updated_by ?? 'New Added' }}</td>

                                    <td class="px-4 py-2">{{ $record->created_at }}</td>

                                   
                                  
                                    <td class="px-4 py-2">{{ $record->updated_at ?? 'New Added' }}</td>
                                
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

                                        <!-- Delete Button
                                        <button wire:click="openChangePasswordModal({{ $record->id }})"
                                            class="bg-red-500 text-white px-3 py-1 rounded-2">
                                            <i class="fas fa-solid fa-key"></i></button> -->
                                    </div>
                                </td>    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            

<!-- Fixed Footer for Row Count -->
<div class="fixed bottom-0 left-0 w-full p-2 z-20">
    <span class="text-sm text-gray-600">Total number of Pricelist : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit PriceList Information</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            @if ($step === 1)

                            {{-- Last Name --}}
                            <div class="form-group">
                                <label style="color:black;">Category</label>
                                <input type="text" wire:model="editPricelist.category_id" class="form-control" readonly disabled>
                            </div>

                            {{-- First Name --}}
                            <div class="form-group">
                                <label style="color:black;">SubCategory</label>
                                <input type="text" wire:model="editPricelist.subcategory_id" class="form-control" readonly disabled> 
                            </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Price (1 pcs)</label>
                                <input type="text" wire:model="editPricelist.price_1" class="form-control" required>
                            </div>


                            {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Price (2 to 50 pcs)</label>
                                <input type="text" wire:model="editPricelist.price_2_50" class="form-control" required>
                            </div>

                             {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Price (51 to 100 pcs)</label>
                                <input type="text" wire:model="editPricelist.price_51_100" class="form-control" required>
                            </div>

                            {{-- Next Button--}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal1()">Cancel</button>
                                <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                            </div>
                            
                            @endif

                            <!-- Page 2 -->
                            @if ($step === 2)

                          

                            {{-- Price (101 to 500) --}}
                            <div class="form-group">
                                <label style="color:black;">Price (101 to 500 pcs)</label>
                                <input type="text" wire:model="editPricelist.price_101_500" class="form-control" required>
                            </div>

                              <div class="form-group">
                                <label style="color:black;">Price ( 501 to 999 pcs)</label>
                                <input type="text" wire:model="editPricelist.price_501_999" class="form-control" required>
                            </div>

                              <div class="form-group">
                                <label style="color:black;">Price (1000 + pcs )</label>
                                <input type="text" wire:model="editPricelist.price_1000_up" class="form-control" required>
                            </div>

                              {{-- Remarks --}}
                            <div class="form-group">
                                <label style="color:black;"><Ri:a>Remarks</Ri:a></label>
                                <input type="text" wire:model="editPricelist.remarks" class="form-control" required>
                            </div>


                            
                            <div class="form-group">
                                <label style="color:black;">Activation</label>
                                <select wire:model="editPricelist.isActive" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
          

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