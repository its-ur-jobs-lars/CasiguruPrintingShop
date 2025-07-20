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
                                <th class="px-4 py-2">Inventory ID</th>
                                <th class="px-4 py-2">Item Name</th>
                                <th class="px-4 py-2">Category</th>
                                <th class="px-4 py-2">Services</th>
                                <th class="px-4 py-2">Unit</th>
                                <th class="px-4 py-2">Quantity</th>
                                <th class="px-4 py-2">Minimum Stock</th>
                                <th class="px-4 py-2">Purchase Price</th>
                                <th class="px-4 py-2">Available Rolls</th>
                                <th class="px-4 py-2">Location</th>
                                 <th class="px-4 py-2">Supplier</th>
                                 <th class="px-4 py-2">Remarks</th>
                                  <th class="px-4 py-2">Added By</th>
                                <th class="px-4 py-2">Updated By</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->inventory_id }}</td>
                                     <td class="px-4 py-2">{{ $record->item_name }}</td>

                                      <td class="px-4 py-2">{{ $category[$record->category_id] ?? 'Not Available' }}</td>

                                      <td class="px-4 py-2">{{ $subcategory[$record->subcategory_id] ?? 'Not Available' }}</td>

                                      <td class="px-4 py-2">{{ $record->unit }}</td>
                                    

                                   <td class="px-4 py-2 {{ $record->quantity < $record->minimum_stock ? 'text-red-600 font-bold' : '' }}">
                                                    {{ $record->quantity }}
                                                </td>

                                                <td class="px-4 py-2">
                                                    {{ $record->minimum_stock }}
                                                </td>
                                   <td class="px-4 py-2">{{ $record->purchase_price }}</td>

                                    <td class="px-4 py-2">{{ $record->available_rolls }}</td>
                                    <td class="px-4 py-2">{{ $record->location }}</td>
                                     <td class="px-4 py-2">{{ $supplier[$record->supplier_id] ?? 'Not Available' }}</td>

                                    <td class="px-4 py-2">{{ $record->remarks }}</td>

                                      <td class="px-4 py-2">{{ $record->added_by ?? 'New Added' }}</td>

                                     <td class="px-4 py-2">{{ $record->updated_by ?? 'New Added' }}</td>

                                
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

                                     
                                @if($hasLowStock)
                                    <button wire:click="loadThreadWithInventoryCheck"
                                            class="bg-blue-500-1 text-white px-3 py-1 rounded-6">
                                        <i class="fa-solid fa-receipt"></i>
                                    </button>
                                @endif
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
    <span class="text-sm text-gray-600">Total number of Inventories : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Inventory Information</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            @if ($step === 1)

                            {{-- Last Name --}}

                             <div class="form-group">
                                <label style="color:black;">Item Name</label>
                                <input type="text" wire:model="editInventory.item_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Category</label>
                                <select wire:model="editInventory.category_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($Category as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subcategory --}}
                            <div class="form-group">
                                <label style="color:black;">SubCategory</label>
                                <select wire:model="editInventory.subcategory_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($SubCategory as $subcategory)
                                        <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Unit</label>
                                <input type="text" wire:model="editInventory.unit" class="form-control" required>
                            </div>


                            {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Quantity</label>
                                <input type="text" wire:model="editInventory.quantity" class="form-control" required>
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
                            

                             {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Minimum Stocks</label>
                                <input type="text" wire:model="editInventory.minimum_stock" class="form-control" required>
                            </div>

                            {{-- Price (101 to 500) --}}
                            <div class="form-group">
                                <label style="color:black;">Purchase Price</label>
                                <input type="text" wire:model="editInventory.purchase_price" class="form-control" required>
                            </div>

                             <div class="form-group">
                                <label style="color:black;">Available Rolls</label>
                                <input type="text" wire:model="editInventory.available_rolls" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Location</label>
                                 <select wire:model="editInventory.location" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="Casiguro Office">Casiguro Office</option>
                                    <option value="Storom Office">Storom Office</option>
                                </select>
                            </div>

                             <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal1()">Cancel</button>
                                <button type="button" class="btn btn-success" wire:click="nextStep1()">Next</button>
                            </div>

                            @endif

                            <!-- Page 3 -->
                            @if ($step === 3)

                             <div class="form-group">
                                <label style="color:black;">Supplier</label>
                                <select wire:model="editInventory.supplier_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($Supplier as $supplier)
                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                              {{-- Remarks --}}
                            <div class="form-group">
                                <label style="color:black;"><Ri:a>Remarks</Ri:a></label>
                                <input type="text" wire:model="editInventory.remarks" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label style="color:black;">Activation</label>
                                <select wire:model="editInventory.isActive" class="form-control" required>
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

            @if (session()->has('UpdateError'))
                <div class="text-red-600 font-semibold text-lg">
                    {{ session('UpdateError') }}
                </div>
            @endif

            {{-- <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded-8" onclick="closeAlertDelete()">OK</button> --}}
        </div>
    </div>
@endif

@if($showThreadPayment)
<div class="cart-overlay">
    <div class="cart-container-1" @click.stop>
        {{-- Header --}}
        <div class="cart-items-scroll-2">
        <div class="cart-header">
             <button wire:click="$set('showThreadPayment', false)" class="cart-close">×</button>
            <h1>OUT OF STOCK ALERT</h1>
        </div>

        {{-- Scrollable List of Items Needing Restock --}}
        <div class="cart-items-scroll-1">
            @php
                $outOfStockItems = collect($showThreadPayment)
                    ->flatMap(fn($group) => $group['items'])
                    ->filter(fn($item) => isset($item['quantity'], $item['minimum_stock']) && $item['quantity'] < $item['minimum_stock']);
            @endphp

            @forelse($outOfStockItems as $item)
                <div class="cart-item border-b border-red-200 bg-red-50 p-4">
                    <div class="cart-left">
                        <div>
                            <div class="item-title font-bold text-red-700">
                                {{ $item['label'] ?? 'Unnamed Category'}}
                            </div>
                            <div class="text-xs text-red-500 italic">
                                Subcategory: {{ $item['subcategory_name'] ?? 'Unknown' }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                              <span class="font-semibold-1">   Stock Available: {{ $item['quantity'] }}</span><br>
                              <span class="font-semibold-2">  Minimum Required: {{ $item['minimum_stock'] }}</span>
                            </div>
                            <div class="text-red-600 mt-2 text-sm font-semibold">
                                This item is below minimum stock and must be restocked.
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded">
                    All items are sufficiently stocked. No issues found.
                </div>
            @endforelse
        </div>

        <!-- {{-- Summary Warning --}}
        @if($outOfStockItems->count() > 0)
            <div class="bg-red-100 text-red-800 px-4 py-3 mt-4 border-t border-red-400 font-semibold text-sm">
                The system has detected items below the required minimum quantity. Please restock before processing further.
            </div>
        @endif -->
        </div>

    </div>
</div>
@endif




<script>
    function closeAlertDelete() {
        document.querySelector('.fixed.inset-0').style.display = 'none';
    }
</script>

</div> 