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
                                <th class="px-4 py-2">Order Reciept ID</th>
                                <th class="px-4 py-2">Order ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">JO Number</th>
                                <th class="px-4 py-2">Deadline</th>
                                <th class="px-4 py-2">Payment Method</th>
                                <th class="px-4 py-2">Reference Number</th>
                                <th class="px-4 py-2">Payment Date</th>
                                <th class="px-4 py-2">Service By</th>
                                <th class="px-4 py-2">Status</th>
                                 <th class="px-4 py-2">Payment Status</th>
                                <th class="px-4 py-2">Remarks</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                     <td class="px-4 py-2">{{ $record->order_receipt_id }}</td>
                                    <td class="px-4 py-2">{{ $record->order_id }}</td>

                                    <td class="px-4 py-2">{{ $record->name }}</td>
                        
                                    <td class="px-4 py-2">{{ $record->jo_number }}</td>
                                    <td class="px-4 py-2">{{ $record->date }}</td>
                                    <td class="px-4 py-2">{{ $record->payment_method }}</td>

                                      <td class="px-4 py-2">{{ $record->reference_number }}</td>
                                       <td class="px-4 py-2">{{ $record->payment_date }}</td>
                                   
                                    <td class="px-4 py-2">{{ $record->service_by }}</td>

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

                                    <td class="px-4 py-2">
                                        @if ($record->payment_status === 'Paid')
                                            <span class="text-green-600 font-semibold" style="color:green">Paid</span> 
                                        @elseif ($record->payment_status === 'Unpaid')
                                            <span class="text-red-600 font-semibold"  style="color:red">Unpaid</span>
                                        @elseif ($record->payment_status === 'Partial')
                                            <span class="text-yellow-600 font-semibold"  style="color:yellow">Partial</span>
                                        @else   
                                            <span class="text-gray-600 font-semibold"  style="color:gray">Unknown</span>
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

                                        <!-- Your new Export button -->
                                    <button wire:click="exportExcel('{{ $record->order_receipt_id }}')"
                                        class="bg-green-600 text-white px-3 py-1 rounded-1">
                                        <i class="fas fa-file-export"></i>
                                    </button>

                                     <button wire:click="exportExcelMultiple('{{ $record->order_id }}')"
                                        class="bg-green-600 text-white px-3 py-1 rounded-1">
                                        <i class="fas fa-file-export"></i>
                                    </button>

                                        <!-- Your new Export button
                                    <button wire:click="exportPDF('{{ $record->order_receipt_id }}')"
                                        class="bg-green-600 text-white px-3 py-1 rounded-1">
                                        <i class="fas fa-file-export"></i>
                                    </button> -->


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
    <span class="text-sm text-gray-600">Total number of Active Receipts : {{ $this->rowCount }}</span>

    
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
                        <h5 class="modal-title" style="color:black;">Edit Request Receipt Information</h5>
                    </div>
                    <div class="modal-body">
                      <form wire:submit.prevent="update">
                            @if ($step === 1)

                       

                            {{-- Deadline --}}
                            <div class="form-group">
                                <label style="color:black;">Deadline</label>
                                <input type="text" wire:model="editOrders.date" class="form-control" required>
                            </div>

                               <div class="form-group">
                                    <label style="color:black;">Payment Method</label>
                                    <select wire:model="editOrders.payment_method" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <option value="Cash">Cash</option>
                                        <option value="GCash">GCash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Reference Number</label>
                                <input type="text" wire:model="editOrders.reference_number" class="form-control" required>
                            </div>


                            {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Payment Date</label>
                                <input type="text" wire:model="editOrders.payment_date" class="form-control" required>
                            </div>

                            {{-- Next Button--}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal1()">Cancel</button>
                                <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                            </div>
                            
                            @endif

                            <!-- Page 2 -->
                            @if ($step === 2)

                          
                            {{-- Payment --}}
                            <div class="form-group">
                                <label style="color:black;">Payment Status</label>
                                <select wire:model="selectedpayment" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Balance">Balance</option>
                                </select>
                            </div>

                            
                            @if($selectedpayment == 'Paid')
                            <div class="form-group" hidden>
                                <input type="text" wire:model="editOrders.payment_status" class="form-control" required readonly>
                            </div>
                            @endif

                            @if($selectedpayment == 'Balance')
                            <div class="form-group">
                                    <label style="color:black;"></label>
                                    <label style="color:black;">Paid Remaining Balance</label>
                                    <input type="text" wire:model="editOrders.payment_status" class="form-control" style="border-color:#darkblue;" required>
                                </div>
                            @endif

                           

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