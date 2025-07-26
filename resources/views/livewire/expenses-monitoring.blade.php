<div>
    {{-- Open Modal Button --}}
    <button wire:click="openModal" class="btn btn-primary">Add Shop Expenses</button>

    {{-- Modal --}}
    @if ($isOpen)
        <div class="modal-backdrop show"></div>
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" style="color:black;">Expenses Monitoring</h5>
                    </div>

                    <div class="modal-body">
                        <form wire:submit.prevent="save">

                            {{-- Step 1 --}}
                            @if ($step === 1)
                                <div class="form-group">
                                    <label style="color:black;">Added Date</label>
                                    <input type="date" wire:model="data.date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">SI / OR Number</label>
                                    <input type="text" wire:model="data.si_or_no" class="form-control" required>
                                </div>

                                  {{-- Subcategory --}}
                            <div class="form-group">
                                <label style="color:black;">Supplier</label>
                                <select wire:model="data.supplier_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    @foreach($Suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                                <div class="form-group">
                                    <label style="color:black;">Particular</label>
                                    <input type="text" wire:model="data.particular" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Amount</label>
                                    <input type="text" wire:model="data.amount" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Quantity</label>
                                    <input type="text" wire:model="data.qty" class="form-control" required>
                                </div>

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="button" class="btn btn-success" wire:click="nextStep">Next</button>
                                </div>
                            @endif

                            {{-- Step 2 --}}
                            @if ($step === 2)
                                <div class="form-group">
                                    <label style="color:black;">SubTotal</label>
                                    <input type="number" wire:model="data.subtotal" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label style="color:black;">Remarks</label>
                                    <input type="text" wire:model="data.remarks" class="form-control" required>
                                </div>

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Save Expense</button>
                                </div>
                            @endif

                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endif


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

            {{-- <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-8" onclick="closeAlertInsert()">OK</button> --}}
        </div>
    </div>
@endif

    <script>
        function closeAlertInsert() {
            const alertBox = document.querySelector('.fixed.inset-0');
            if (alertBox) {
                alertBox.style.display = 'none';
            }
        }
    </script>

    @livewireScripts
</div>
