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
                                <th class="px-4 py-2">PO Number</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Payment Date</th>
                                <th class="px-4 py-2">Process Date</th>
                                <th class="px-4 py-2">Title</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Payment</th>
                                <th class="px-4 py-2">Payment Method</th>
                                <th class="px-4 py-2">Payment Balance</th>
                                 <th class="px-4 py-2">Payment Status</th>
                                 <th class="px-4 py-2">Remarks</th>
                                <th class="px-4 py-2">Document Status</th>
                                <th class="px-4 py-2">Status</th>
                                  <th class="px-4 py-2">Added By</th>
                                <th class="px-4 py-2">Updated By</th>
                                <th class="px-4 py-2">Activation</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $record->order_id }}</td>
                                     <td class="px-4 py-2">{{ $record->po_number }}</td>

                                      <td class="px-4 py-2">{{ $record->name }}</td>
                                    

                                   <td class="px-4 py-2">{{ $record->payment_date }}</td>

                                    <td class="px-4 py-2">{{ $record->process_date }}</td>
                                    <td class="px-4 py-2">{{ $record->title }}</td>
                                    <td class="px-4 py-2">{{ $record->total }}</td>

                                      <td class="px-4 py-2">{{ $record->payment }}</td>
                                    

                                   <td class="px-4 py-2">{{ $record->payment_method }}</td>

                                    <td class="px-4 py-2">{{ $record->payment_balance }}</td>
                                    <td class="px-4 py-2">{{ $record->payment_status }}</td>
                                    <td class="px-4 py-2">{{ $record->remarks }}</td>

                                      <td class="px-4 py-2">{{ $record->document_status }}</td>
                                    <td class="px-4 py-2">{{ $record->status }}</td>

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
    <span class="text-sm text-gray-600">Total number of Upaid Orders : {{ $this->rowCount }}</span>
</div>
       
            {{-- Edit Function --}}
        @if($isEditModalOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Edit Government Payables</h5>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="update">
                            {{-- Step 1 --}}
                            @if ($step === 1)

                                <div class="form-group">
                                    <label style="color:black;">Order ID</label>
                                    <input type="text" wire:model="editGovernDetails.order_id" class="form-control" required>
                                </div>

                            

                                <div class="form-group">
                                    <label style="color:black;">PO Number</label>
                                    <input type="text" wire:model="editGovernDetails.po_number" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Customer Name</label>
                                    <input type="text" wire:model="editGovernDetails.name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Title</label>
                                    <input type="text" wire:model="editGovernDetails.title" class="form-control" required>
                                </div>

                                 
                                <div class="form-group">
                                    <label style="color:black;">Total Amount</label>
                                    <input type="text" wire:model="editGovernDetails.total" class="form-control" required>
                                </div>

                                <div class="form-group">
                                        <label style="color:black;">Payment Method</label>
                                        <select wire:model="editGovernDetails.payment_method" class="form-control" required>
                                            <option value="">-- Select --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="GCash">GCash</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cheque">Cheque</option>
                                        </select>
                                    </div>


                                

                                

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                                </div>
                            @endif

                            {{-- Step 2 --}}
                            @if ($step === 2)

                                 <div class="form-group">
                                    <label style="color:black;">Payment Date</label>
                                    <input type="date" wire:model="editGovernDetails.payment_date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Process Date</label>
                                    <input type="date" wire:model="editGovernDetails.process_date" class="form-control" required>
                                </div>


                                <div class="form-group">
                                    <label style="color:black;">Payment Balance</label>
                                    <input type="text" wire:model="editGovernDetails.payment_balance" class="form-control" required>
                                </div>

                               {{-- Payment Type --}}
                                    <div class="form-group">
                                        <label style="color:black;">Payment Status</label>
                                        <select wire:model="editGovernDetails.payment_status" class="form-control" required>
                                            <option value="">-- Select --</option>
                                            <option value="Partial">Partial</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>

                                    

                                    <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep1()">Next</button>
                                </div>

                            @endif
                            {{-- Step 3 --}}
                            @if ($step === 3)

                                 <div class="form-group">
                                    <label style="color:black;">Remarks</label>
                                    <input type="text" wire:model="editGovernDetails.remarks" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Status</label>
                                    <select wire:model.defer="editGovernDetails.status" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <option value="Pending">Pending</option>
                                         <option value="Pending">Layouting</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Printing">Printing</option>
                                        <option value="For PickUp">For PickUp</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label  style="color:black;">Government Payment Step</label>
                                    <select wire:model="editGovernDetails.document_status" class="form-control" required>
                                        <option value="">-- Select Government Payment Step --</option>
                                        <option value="Purchase Request (PR)">Purchase Request (PR)</option>
                                        <option value="Purchase Order (PO) or Job Order">Purchase Order (PO) or Job Order</option>
                                        <option value="Delivery of Goods/Services">Delivery of Goods/Services</option>
                                        <option value="Inspection & Acceptance">Inspection & Acceptance</option>
                                        <option value="Billing / Invoice Submission">Billing / Invoice Submission</option>
                                        <option value="Document Review & Processing">Document Review & Processing</option>
                                        <option value="Obligation Request and Status (ORS)">Obligation Request and Status (ORS)</option>
                                        <option value="Disbursement Voucher (DV) Preparation">Disbursement Voucher (DV) Preparation</option>
                                        <option value="Check/EFT Issuance">Check/EFT Issuance</option>
                                        <option value="Issuance of Official Receipt">Issuance of Official Receipt</option>
                                    </select>
                                </div>

                                  <div class="form-group">
                                <label style="color:black;">Status</label>
                               <select wire:model="editGovernDetails.isActive" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                    <button type="submit" class="btn btn-success">Save Employee Benefits</button>
                                </div>
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




<script>
    function closeAlertDelete() {
        document.querySelector('.fixed.inset-0').style.display = 'none';
    }
</script>

</div> 