<div>
    {{-- <h2 class="text-lg font-semibold mb-4">Laptop Inventory</h2> --}}

    
        <!-- Search Filter -->
        <input type="text" wire:model.debounce.500ms="search" placeholder="Search..."
            class="border border-gray-300 rounded-9 px-3 py-2 w-full">
    
          
        <select wire:model="filterActivation" class="border border-gray-300 rounded px-3 py-2 w-full mb-4">
            <option value="1">Active</option>
            <option value="0">Inactive</option>   
        </select>
               
            <select wire:model="paymentMethodFilter" class="border border-gray-300 rounded px-3 py-2 w-full mb-4">
        <option value="">-- Filter by Payment Method --</option>
        <option value="Cash">Cash</option>
        <option value="GCash">GCash</option>
        <option value="Bank Transfer">Bank Transfer</option>
        <option value="Cheque">Cheque</option>
    </select>

    <div class="overflow-x-auto-1 bg-white shadow-md rounded-lg relative">
        <div class="overflow-y-auto max-h-[500px]">
            <table class="min-w-full border-collapse">
                <thead class="sticky top-0 bg-gray-100 z-10">
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Payment ID</th>
                        <th class="px-4 py-2">Order ID</th>
                        <th class="px-4 py-2">SubCategory Name</th>
                        <th class="px-4 py-2">JO Number</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Address</th>
                        <th class="px-4 py-2">Amount</th>
                        <th class="px-4 py-2">Payment</th>
                        <th class="px-4 py-2">Balance</th>
                        <th class="px-4 py-2">Total</th>
                        <th class="px-4 py-2">Payment Method</th>
                        @if ($paymentMethodFilter === 'Cash')
                            <th class="px-4 py-2">Receipt No.</th>
                            <th class="px-4 py-2">Received By</th>
                        @elseif ($paymentMethodFilter === 'GCash')
                            <th class="px-4 py-2">GCash No.</th>
                            <th class="px-4 py-2">GCash Account</th>
                        @elseif ($paymentMethodFilter === 'Bank Transfer')
                            <th class="px-4 py-2">Bank Name</th>
                        @elseif ($paymentMethodFilter === 'Cheque')
                            <th class="px-4 py-2">Cheque No.</th>
                            <th class="px-4 py-2">Cheque Date</th>
                            <th class="px-4 py-2">Bank Name</th>
                        @endif
                        <th class="px-4 py-2">Reference No.</th>
                        <th class="px-4 py-2">Payment Date</th>
                        <th class="px-4 py-2">Payment Status</th>
                        <th class="px-4 py-2">Remarks</th>
                        <th class="px-4 py-2">Service By</th>
                        <th class="px-4 py-2">Activation</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        @if ($paymentMethodFilter === '' || $record->payment_method === $paymentMethodFilter)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $record->payment_id }}</td>
                            <td class="px-4 py-2">{{ $record->order_id }}</td>
                            <td class="px-4 py-2">{{ $SubCategory[$record->subcategory_id] ?? 'Not Available' }}</td>
                            <td class="px-4 py-2">{{ $record->jo_number }}</td>
                            <td class="px-4 py-2">{{ $record->name }}</td>
                            <td class="px-4 py-2">{{ $record->address }}</td>
                            <td class="px-4 py-2">{{ $record->amount }}</td>
                            <td class="px-4 py-2">{{ $record->payment }}</td>
                            <td class="px-4 py-2">{{ $record->balance }}</td>
                            <td class="px-4 py-2">{{ $record->total }}</td>
                            <td class="px-4 py-2">{{ $record->payment_method }}</td>

                            @if ($paymentMethodFilter === 'Cash')
                                <td class="px-4 py-2">{{ $record->reference_number }}</td>
                                <td class="px-4 py-2">{{ $record->cash_received_by }}</td>
                            @elseif ($paymentMethodFilter === 'GCash')
                                <td class="px-4 py-2">{{ $record->gcash_number }}</td>
                                <td class="px-4 py-2">{{ $record->gcash_account_name }}</td>
                            @elseif ($paymentMethodFilter === 'Bank Transfer')
                                <td class="px-4 py-2">{{ $record->bank_name }}</td>
                            @elseif ($paymentMethodFilter === 'Cheque')
                                <td class="px-4 py-2">{{ $record->cheque_number }}</td>
                                <td class="px-4 py-2">{{ $record->cheque_date }}</td>
                                <td class="px-4 py-2">{{ $record->bank_name }}</td>
                            @endif

                            <td class="px-4 py-2">{{ $record->reference_number }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($record->payment_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">{{ $record->payment_status }}</td>
                            <td class="px-4 py-2">{{ $record->remarks }}</td>
                            <td class="px-4 py-2">{{ $record->service_by }}</td>
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

                                <button wire:click="showThread({{ $record->id }})"
                                    class="bg-blue-500-1 text-white px-3 py-1 rounded-6">
                                    <i class="fa-solid fa-receipt"></i></button>
                                        <!-- Delete Button
                                        <button wire:click="openChangePasswordModal({{ $record->id }})"
                                            class="bg-red-500 text-white px-3 py-1 rounded-2">
                                            <i class="fas fa-solid fa-key"></i></button> -->
                                    </div>
                                </td>    
                            </tr>
                            @endif
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


                             <!-- Last Name
                            <div class="form-group">
                                <label style="color:black;">Quantity</label>
                                <input type="text" wire:model="editOrders.qty" class="form-control" required>
                            </div>

                            {{-- First Name --}}
                            <div class="form-group">
                                <label style="color:black;">Price</label>
                                <input type="text" wire:model="editOrders.price" class="form-control" required> 
                            </div> -->

                            {{-- Middle Name --}}
                            <div class="form-group">
                                <label style="color:black;">Amount</label>
                                <input type="text" wire:model="editOrders.amount" class="form-control" required>
                            </div>


                            {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Downpayment</label>
                                <input type="text" wire:model="editOrders.payment" class="form-control" required>
                            </div>

                              {{-- Price (51 to 100) --}}
                            <div class="form-group">
                                <label style="color:black;">Total Amount</A></label>
                                <input type="text" wire:model="editOrders.total" class="form-control" required>
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
                            <div class="form-group">
                                <label style="color:black;">Balance</label>
                                <input type="text" wire:model="editOrders.balance" class="form-control" required>
                            </div>

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

    @if($showPaymentThread)
<div class="cart-overlay">
    <div class="cart-container-1" @click.stop>
        {{-- Header --}}
        <div class="cart-items-scroll-2">
            <div class="cart-header">
                <h1>PAYMENT THREAD</h1>
            </div>

            {{-- Scrollable Payment Records --}}
            <div class="cart-items-scroll-1">
                @forelse($showThreadPayment as $index => $group)
                    {{-- Header for this group --}}
                    <div class="color px-4 py-2 border-b border-gray-300 bg-gray-100 font-semibold">
                        {{ $group['items'][0]['label'] ?? '' }} - {{ \Carbon\Carbon::parse($group['date'])->format('M d, Y') }}
                    </div>

                    @foreach ($group['items'] as $payment)
                        <div class="cart-item">
                            <button wire:click="$set('showPaymentThread', false)" class="cart-close">×</button>

                            <div class="cart-left">
                                <div>
                                    <div class="item-title font-semibold">
                                        {{ $payment['label'] }} — {{ $payment['name'] }}
                                    </div>

                                    <div class="text-xs text-gray-500 italic">{{ $payment['subcategory_name'] }}</div>
                                     <div class="text-xs text-gray-500 italic">{{ $payment['payment_method'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="cart-right text-right text-sm">
                                <div class="blue">Total: ₱{{ number_format($payment['total'], 2) }}</div>
                                <div class="green">Paid: ₱{{ number_format($payment['payment'], 2) }}</div>
                                <div class="red">Balance: ₱{{ number_format($payment['balance'], 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <p class="cart-empty">No payment records yet.</p>
                @endforelse
            </div>

            {{-- Order ID Summary --}}
            <div class="color cart-summary mt-4 px-4 py-2 border-t border-gray-300 bg-gray-100 text-sm font-semibold">
                <div class="flex justify-between">
                    <span>Order ID:</span>
                    <span>{{ $threadOrderId ?? 'N/A' }}</span>
                </div>
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