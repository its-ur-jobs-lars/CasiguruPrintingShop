<div>
    @if(session()->has('messageInsert'))
        <div class="bg-green-500 text-black p-2 rounded mb-2">
            {{ session('messageInsert') }}
        </div>
    @endif

    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Request Order Receipt</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Request Order Receipt</h5>
                        <button type="button" class="close" wire:click="closeModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            {{-- Step 1 --}}
                            @if ($step === 1)

                              <select wire:model="order_id" class="form-control" required>
                            <option value="">-- Select Order --</option>
                            @foreach($Order->unique('order_id') as $order)
                                <option value="{{ $order->order_id }}">
                                    {{ $order->order_id }} - {{ $order->name }}
                                </option>
                            @endforeach
                        </select>

                              
                            @if($order_id)
                                <select wire:model="selected_subcategory_id" class="form-control" required>
                                    <option value="">-- Select Product/Subcategory --</option>
                                    @forelse($filteredSubCategories as $subcategory)
                                        <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                                    @empty
                                        <option value="">No subcategories found for this order</option>
                                    @endforelse
                                </select>
                            @endif

                                <div class="form-group">
                                    <label style="color:black;">Customer Name</label>
                                    <input type="text" wire:model="data.name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Customer Contact No.</label>
                                    <input type="text" wire:model="data.contact_no" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Customer Address</label>
                                    <input type="text" wire:model="data.address" class="form-control" required>
                                </div>

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

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                                </div>
                            @endif

                            {{-- Step 2 --}}
                            @if ($step === 2)
                                <div class="form-group">
                                    <label style="color:black;">Quantity</label>
                                    <input type="text" wire:model="data.qty" class="form-control" required>
                                </div>

                                 <div class="form-group">
                                    <label style="color:black;">Layout Fee</label>
                                    <input type="text" wire:model="data.layout_fee" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Price</label>
                                    <input type="text" wire:model="data.price" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Amount</label>
                                    <input type="text" wire:model="data.amount" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Total Amount</label>
                                    <input type="text" wire:model="data.total" class="form-control" readonly>
                                </div>

                               

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep1()">Next</button>
                                </div>
                            @endif

                            {{-- Step 3 --}}
                            @if ($step === 3)

                            @if (!$isGovernment)

                                   <div class="form-group">
                                    <label style="color:black;">Receipt Number</label>
                                    <input type="text" wire:model="data.receipt_number" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Payment</label>
                                    <input type="text" wire:model="data.payment" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Balance</label>
                                    <input type="text" wire:model="data.balance" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">JO Number</label>
                                    <input type="text" wire:model="data.jo_number" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Reference</label>
                                    <input type="text" wire:model="data.reference_number" class="form-control" required>
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
                                @endif


                                  <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep2()">Next</button>
                                </div>
                            @endif
                            
                             {{-- Step 3 --}}
                            @if ($step === 4)

                                @if (!$isGovernment)
                                <div class="form-group">
                                    <label style="color:black;">Payment Date</label>
                                    <input type="date" wire:model="data.payment_date" class="form-control" required>
                                </div>

                                {{-- Payment --}}
                <div class="form-group">
                    <label style="color:black;">Payment Status</label>
                    <select wire:model="selectedpayment" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial">Partial</option>
                    </select>
                </div>

                {{-- Set payment_status automatically --}}
                <input type="hidden" wire:model="data.payment_status">

                @if($selectedpayment == 'Partial')
                    <div class="form-group">
                        <label style="color:black;">Paid Remaining Balance</label>
                        <input type="text" wire:model="data.balance" class="form-control" readonly>
                    </div>
                @endif

                                  <div class="form-group">
                                    <label style="color:black;">Status</label>
                                    <select wire:model="data.status" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Printing">Printing</option>
                                        <option value="For PickUp">For PickUp</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Remarks</label>
                                    <input type="text" wire:model="data.remarks" class="form-control" required>
                                </div>
                                @endif


                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                    <button type="submit" class="btn btn-success">Save Receipt</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- {{-- Feedback Alert --}}
    @if (session()->has('messageInsert') || session()->has('errorInsert'))
        <div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
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
            </div>
        </div>
    @endif

    {{-- Auto-hide success/fail modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const alertBox = document.querySelector('.fixed.inset-0');
                if (alertBox) {
                    alertBox.style.display = 'none';
                }
            }, 3000);
        });
    </script> -->

    @livewireScripts
</div>
