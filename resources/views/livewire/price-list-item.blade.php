<div>
    {{-- Button to Open Modal --}}
    <button wire:click="openModal" class="btn btn-primary">Add PriceList</button>

    {{-- Product Form Modal --}}
    @if($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Add Pricelist</h5>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            
                          @if ($step === 1)
                            {{-- Category --}}
                            <div class="form-group">
                                <label style="color:black;">Category</label>
                                <select wire:model="data.category_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($Category as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subcategory --}}
                            <div class="form-group">
                                <label style="color:black;">SubCategory</label>
                                <select wire:model="data.subcategory_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($SubCategory as $subcategory)
                                        <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                                    @endforeach
                                </select>
                            </div>


                                <div class="form-group">
                                    <label style="color:black;">Price (1 pc)</label>
                                    <input type="text" wire:model="data.price_1" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label style="color:black;">Price (2 to 50 pcs)</label>
                                    <input type="text" wire:model="data.price_2_50" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label style="color:black;">Price (51 to 100 pcs)</label>
                                   <input type="text" wire:model="data.price_51_100" class="form-control" required>
                                </div>

                                   <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep()">Next</button>
                                </div>

                                @endif

                                @if ($step === 2)
                                <div class="form-group">
                                    <label style="color:black;">Price (101 to 500 pcs)</label>
                                    <input type="text" wire:model="data.price_101_500" class="form-control" required>
                                </div>
                            
                                <div class="form-group">
                                    <label style="color:black;">Price (501 to 999 pcs)</label>
                                    <input type="text" wire:model="data.price_501_999" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label style="color:black;">Price (1000 + pcs)</label>
                                    <input type="text" wire:model="data.price_1000_up" class="form-control" required>
                                </div>
                              

                            {{-- Remarks --}}
                            <div class="form-group">
                                <label style="color:black;">Remarks</label>
                                <input type="text" wire:model="data.remarks" class="form-control" required>
                            </div>

                            {{-- Buttons --}}
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep()">Previous</button>
                                <button type="submit" class="btn btn-success">Save Pricelist</button>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Success/Error Message Modal --}}
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

    <script>
        function closeAlertInsert() {
            document.querySelector('.fixed.inset-0').style.display = 'none';
        }
    </script>

    @livewireScripts
</div>
