<div>
    
    {{-- Product List --}}

  
    @if($showOrder)
    <div class="bg-white rounded shadow p-6 w-1/2">

<!-- Search Filter -->
<div class="relative w-full">
    <input type="text" wire:model.debounce.500ms="search" placeholder="Search..."
        class="border border-gray-300 rounded-9 px-3 py-2 w-full pr-10" />
    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
        <i class="fas fa-magnifying-glass text-gray-400"></i>
    </span>
</div>
          
        
        @if($groupedItems->isEmpty())
        <div class="flex px-4 py-4 gap-6 overflow-x-auto">
            <div class="text-center text-gray-500 mt-4-1">
                No items found for "{{ $search }}"
            </div>
       </div>
        @else
        @foreach ($groupedItems as $categoryName => $categoryItems)
             <div class="flex px-4 py-4 gap-6 overflow-x-auto">
                <!-- Category Header -->
                <h2 class="text-xl font-bold mb-4">{{ $categoryName }}</h2>
                <!-- Horizontal Product Row -->
                 <div class="flex justify-start px-4-1 py-4 space-x-6 overflow-x-auto">
                 
                    @foreach ($categoryItems as $item)
                        <div class="w-60 bg-white rounded-lg shadow p-4-1 flex-shrink-0">
                           <img src="{{ asset('storage/' . $item->subcategory->image) }}"
                                alt="{{ $item->subcategory->subcategory_name }}"
                                class="h-24 w-full object-contain mb-2-3"
                                style="max-height: 150px; max-width: 150px;">
                            <h3 class="font-semibold font-bold px-4 mb-2-3">{{ $item->subcategory->subcategory_name ?? 'No Subcategory' }}</h3>
                            <div class="text-sm space-y-1 mb-2">
                                <!-- <p class="text-green-600 font-bold">₱{{ number_format($item->price_10_50, 2) }} <span class="text-gray-500 text-xs">(10–50 pcs)</span></p>
                                <p class="text-green-600 font-bold">₱{{ number_format($item->price_51_100, 2) }} <span class="text-gray-500 text-xs">(51–100 pcs)</span></p>
                                <p class="text-green-600 font-bold">₱{{ number_format($item->price_101_500, 2) }} <span class="text-gray-500 text-xs">(101–500 pcs)</span></p> -->
                            </div>
                            <button wire:click="addToCart({{ $item->id }})"
                                    class="mt-2-1 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                                Add to Cart
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
            @endif
            
     </div>
    @endif
   
    {{-- Cart Overlay --}}
@if($showCart)
<div class="cart-overlay">
    <div class="cart-container">

        {{-- Header with Close Button --}}
        <div class="cart-header">
            <h1>ORDER</h1>
            <button wire:click="$set('showCart', false)" class="cart-close">✕</button>
        </div>

        {{-- Scrollable Item List --}}
        <div class="cart-items-scroll">
            @forelse($cart as $id => $c)
                <div class="cart-item">
                    <div class="cart-left">
                        <img src="{{ asset('storage/' . ($c['image'] ?? 'default.png')) }}"
                             alt="{{ $c['subcategory_name'] ?? 'No Subcategory' }}"
                             class="cart-image">
                        <div>
                            <div class="item-title">{{ $c['subcategory_name'] ?? 'No Subcategory' }}</div>
                        </div>
                    </div>

                    <div class="cart-right">
                        <div class="item-price">₱{{ number_format($c['price'], 2) }}</div>
                        <div class="item-subtotal">₱{{ number_format($c['price'] * $c['qty'], 2) }}</div>
                        <div class="qty-control">
                            <button wire:click="decrementQty({{ $id }})">-</button>
                             <input type="number" wire:model.defer="cart.{{ $id }}.qty" min="1" placeholder="Qty">
                              
                            <button wire:click="incrementQty({{ $id }})">+</button>
                        </div>
                    </div>
                   
                     </div>
                       <div class="mt-1">
                        <label class="text-xs block text-gray-500">Layout Fee</label>
                       <select
                            wire:model.lazy="cart.{{ $id }}.layout_option"
                            class="form-select w-full text-sm text-gray-700">
                            <option value="">-- Select --</option>
                            <option value="with_fee">With Layout Fee</option>
                            <option value="no_fee">No Layout Fee</option>
                        </select>

                        @if(($cart[$id]['layout_option'] ?? '') === 'with_fee')
                            <input
                                type="number"
                                wire:model.lazy="cart.{{ $id }}.layout_fee"
                                placeholder="₱0.00"
                                class="form-input mt-1 w-24 text-right"
                                min="0"
                            />
                        @endif
                    </div>
     
            @empty
                <p class="cart-empty">Your cart is empty.</p>
            @endforelse
        </div>

        {{-- Summary --}}
        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>
                    ₱{{ number_format(collect($cart)->sum(fn($c) => ($c['price'] * $c['qty'])), 2) }}
                </span>
            </div>

            <div class="summary-row">
                <span>Layout Fee</span>
                <span>
                    ₱{{ number_format(collect($cart)->sum(fn($c) => $c['layout_fee'] ?? 0), 2) }}
                </span>
            </div>

            <div class="summary-row total font-bold">
                <span>Total</span>
                <span>
                    ₱{{ number_format(collect($cart)->sum(fn($c) => ($c['price'] * $c['qty']) + ($c['layout_fee'] ?? 0)), 2) }}
                </span>
            </div>


        <button wire:click="openModal" class="checkout-button">
            PROCEED TO CHECKOUT
        </button>
    </div>
</div>
@endif


    {{-- Order Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-700 bg-opacity-75 flex items-center justify-center z-50">
            <div class="showModal rounded shadow p-6 w-1/2">
           
        <h4 class="mb-4 text-left">Customer Details</h4>
        <form wire:submit.prevent="save">
            @if (session()->has('messageInsert'))
                <div class="alert alert-success">{{ session('messageInsert') }}</div>
            @endif

                {{-- Name --}}
                <div class="form-row mb-3" style="display: flex; gap: 10px;">
                    <div class="col">
                        <label for="name" style="float: left;">Customer Name</label>
                      <input type="text" wire:model.defer="data.name" class="form-control" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                      <div class="col">
                        <label for="contact_no" style="float: left;">Customer Contact Number</label>
                        <input type="text" wire:model.defer="data.contact_no" class="form-control" required>
                        @error('contact_no') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                       <div class="col">
                        <label for="address" style="float: left;">Address</label>
                        <input type="text" wire:model.defer="data.address" class="form-control" required>
                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    </div>
                    

                 <div class="form-row mb-3" style="display: flex; gap: 10px;">
                    <div class="col">
                          <label for="deadline" style="float: left;">Deadline</label>
                         <input type="date" wire:model.defer="data.deadline" class="form-control" required>
                        @error('deadline') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                        <div class="form-group">
                                    <label>Status</label>
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
                        <label for="remarks" style="float: left;">Remarks</label>
                        <input type="text" wire:model.defer="data.remarks" class="form-control" required>
                        @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                 </div>

                   <div class="form-group">
                                    <label>Customer Type</label>
                                    <select wire:model.defer="data.customer_type" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <option value="Walk-in">Walk-in</option>
                                        <option value="Government">Government</option>

                                    </select>
                                </div>
                      
                 </div>
                
               <button wire:click="save"
                        class="mt-2-1 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                    Submit Order
                </button>
                <button wire:click="closeModal" 
                        class="mt-2-1 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                    Cancel
                </button>
                                
            </form>
        </div>


    @endif

    
    {{-- Success/Error Messages --}}
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
            </div>
        </div>
    @endif
</div>