<div>
    @if(session()->has('messageInsert'))
        <div class="bg-green-500 text-black p-2 rounded mb-2">
            {{ session('messageInsert') }}
        </div>
    @endif

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Track Government Payables</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Track Government Payables</h5>
                        <button type="button" class="close" wire:click="closeModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            {{-- Step 1 --}}
                            @if ($step === 1)

                              <div class="form-group">
                                <label for="order_id">Order </label>
                                <select wire:model="order_id" class="form-control" required>
                            <option value="">-- Select Order --</option>
                            @foreach($Order->unique('order_id') as $order)
                                <option value="{{ $order->order_id }}">
                                    {{ $order->order_id }} - {{ $order->name }}
                                </option>
                            @endforeach
                        </select>
                            </div>

                            

                                <div class="form-group">
                                    <label style="color:black;">PO Number</label>
                                    <input type="text" wire:model="data.po_number" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Customer Name</label>
                                    <input type="text" wire:model="data.name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Title</label>
                                    <input type="text" wire:model="data.title" class="form-control" required>
                                </div>

                                 
                                <div class="form-group">
                                    <label style="color:black;">Total Amount</label>
                                    <input type="text" wire:model="data.total" class="form-control" required>
                                </div>

                                <div class="form-group">
                                        <label style="color:black;">Payment Method</label>
                                        <select wire:model="data.payment_method" class="form-control" required>
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
                                    <input type="date" wire:model="data.payment_date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Process Date</label>
                                    <input type="date" wire:model="data.process_date" class="form-control" required>
                                </div>


                                <div class="form-group">
                                    <label style="color:black;">Payment Balance</label>
                                    <input type="text" wire:model="data.payment_balance" class="form-control" required>
                                </div>

                               {{-- Payment Type --}}
                                    <div class="form-group">
                                        <label style="color:black;">Payment Status</label>
                                        <select wire:model="data.payment_status" class="form-control" required>
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
                                    <input type="text" wire:model="data.remarks" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Status</label>
                                    <select wire:model.defer="data.status" class="form-control" required>
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
                                    <select wire:model="data.document_status" class="form-control" required>
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

   

    @livewireScripts
</div>
