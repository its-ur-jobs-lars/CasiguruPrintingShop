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
                                <th class="px-4 py-2">Order ID</th>
                                <th class="px-4 py-2"> Name</th>
                                <th class="px-4 py-2">Contact Number</th>
                                <th class="px-4 py-2">Address</th>
                                <th class="px-4 py-2">Category</th>
                                <th class="px-4 py-2">SubCategory</th>
                                <th class="px-4 py-2">Quantity</th>
                                <th class="px-4 py-2">Price</th>
                                 <th class="px-4 py-2">Amount</th>
                                <th class="px-4 py-2">Layout Fee</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Payment</th>
                                <th class="px-4 py-2">Payment Method</th>
                                <th class="px-4 py-2">Balance</th>
                                <th class="px-4 py-2">JO Number</th>
                                <th class="px-4 py-2">Deadline</th>
                                <th class="px-4 py-2">Added By</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Remarks</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->order_id }}</td>

                                    <td class="px-4 py-2">{{ $record->name }}</td>
                                    <td class="px-4 py-2">{{ $record->contact_no}}</td>
                                    <td class="px-4 py-2">{{ $record->address }}</td>

                                      <td class="px-4 py-2">{{ $Category[$record->category_id] ?? 'Not Available' }}</td>

                                      <td class="px-4 py-2">{{ $SubCategory[$record->subcategory_id] ?? 'Not Available' }}</td>

                                    <td class="px-4 py-2">{{ $record->qty }}</td>
                                    <td class="px-4 py-2">{{ $record->price }}</td>
                                    <td class="px-4 py-2">{{ $record->amount }}</td>
                                    <td class="px-4 py-2">{{ $record->layout_fee }}</td>
                                    <td class="px-4 py-2">{{ $record->total }}</td>
                                    
                                    @if ($record->Payment)
                                        <td class="px-4 py-2">{{ $record->Payment->payment }}</td>
                                        <td class="px-4 py-2">{{ $record->Payment->payment_method }}</td>
                                        <td class="px-4 py-2">{{ $record->Payment->balance }}</td>
                                    @else
                                        <td class="px-4 py-2 text-gray-400">No payment</td>
                                        <td class="px-4 py-2 text-gray-400">N/A</td>
                                        <td class="px-4 py-2 text-gray-400">N/A</td>
                                    @endif
                                    <td class="px-4 py-2">{{ $record->jo_number }}</td>
                                    <td class="px-4 py-2">{{ $record->deadline }}</td>
                                   
                                    <td class="px-4 py-2">{{ $record->added_by }}</td>

                                     <td class="px-4 py-2">
                                        @if ($record->status === 'Pending')
                                            <span class="text-yellow-600 font-semibold">Pending</span>
                                        @elseif ($record->status === 'Completed')
                                            <span class="text-green-600 font-semibold">Completed</span>
                                        @elseif ($record->status === 'Cancelled')
                                            <span class="text-red-600 font-semibold">Cancelled</span>
                                        @elseif ($record->status === 'Printing')
                                            <span class="text-blue-600 font-semibold">Printing</span>
                                        @elseif ($record->status === 'For PickUp')
                                            <span class="text-purple-600 font-semibold">For PickUp</span>
                                        @else
                                            <span class="text-gray-600 font-semibold">Unknown</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-4 py-2">{{ $record->remarks}}</td>
                                
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
    <span class="text-sm text-gray-600">Total number of Active Orders : {{ $this->rowCount }}</span>

     <input type="date" id="date_to" wire:model="date_to" class="border border-gray-300 rounded-12 px-3 py-2" placeholder="To" style="float: right;">
    <label for="date_to" class="mb-0 mr-2" style="color: black; float: right;">To:</label>
    <input type="date" id="date_from" wire:model="date_from" class="border border-gray-300 rounded-12 px-3 py-2 mr-3" placeholder="From" style="float: right;">
    <label for="date_from" class="mb-0 mr-2" style="color: black; float: right;">From:</label>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Order Information</h5>
                    </div>
                    <div class="modal-body">
                      <form wire:submit.prevent="update">
                            @if ($step === 1)

                            {{--<div class="form-group">
                                    <label style="color:black;">Category</label>
                                    <select wire:model="editOrders.category_id" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        @foreach($Category as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">SubCategory</label>
                                    <select wire:model="editOrders.subcategory_id" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        @foreach($SubCategory as $subcategory)
                                            <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}


                            {{-- Last Name --}}
                            <div class="form-group">
                                <label style="color:black;">Quantity</label>
                                <input type="text" wire:model="editOrders.qty" class="form-control" readonly disabled>
                            </div>

                            {{-- First Name --}}
                            <div class="form-group">
                                <label style="color:black;">Price</label>
                                <input type="text" wire:model="editOrders.price" class="form-control" readonly disabled> 
                            </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Amount</label>
                                <input type="text" wire:model="editOrders.amount" class="form-control" readonly disabled>
                            </div>


                            <!-- {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Downpayment</label>
                                <input type="text" wire:model="editOrders.payment" class="form-control" required>
                            </div> -->

                              {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Total Amount</A></label>
                                <input type="text" wire:model="editOrders.total" class="form-control" readonly disabled>
                            </div>

                            {{-- Next Button--}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
                                <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                            </div>
                            
                            @endif

                            <!-- Page 2 -->
                            @if ($step === 2)

                          

                            {{-- Price (101 to 500) --}}
                            {{--<div class="form-group">
                                <label style="color:black;">Balance</label>
                                <input type="text" wire:model="editOrders.balance" class="form-control" required>
                            </div>--}}

                              {{-- Remarks --}}
                            <div class="form-group">
                                <label style="color:black;"><Ri:a>Deadline</Ri:a></label>
                                <input type="date" wire:model="editOrders.deadline" class="form-control" required>
                            </div>

                             {{-- Remarks --}}
                            <div class="form-group">
                                <label style="color:black;"><Ri:a>Status</Ri:a></label>
                                <select wire:model="editOrders.status" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Printing">Printing</option>
                                        <option value="For PickUp">For PickUp</option>

                                    </select>
                            </div>

                             {{-- Remarks --}}
                            <div class="form-group">
                                <label style="color:black;"><Ri:a>Remarks</Ri:a></label>
                                <input type="text" wire:model="editOrders.remarks" class="form-control" required>
                            </div>


                            
                            <div class="form-group">
                                <label style="color:black;">Activation</label>
                                <select wire:model="editOrders.isActive" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
          

                            {{-- Submit Button --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                               {{-- <button wire:click="update()" class="bg-green-500 text-white px-3 py-1 rounded-3 mt-2">Apply changes</button> --}}
                               <button type="submit"  class="btn btn-success">Apply changes</button>
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