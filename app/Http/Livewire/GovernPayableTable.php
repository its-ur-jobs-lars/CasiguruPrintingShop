<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TextFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\WithPagination;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\GovernPayable;
use Illuminate\Support\Facades\Auth;
use App\Models\payment;
use App\Models\User;

class GovernPayableTable extends Component implements HasTable
{
    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

    protected $listeners = ['refreshComponent' => '$refresh'];

        public function nextStep() { $this->step = 2; }
        public function previousStep() { $this->step = 1; }
        public function nextStep1() { $this->step = 3; }
        public function nextStep2() { $this->step = 3; }
        public function previousStep1() { $this->step = 2; }

    public $search = '';

    public $selectedSerialNumber = '';

    public $newSerialNumber = '';

    public $updatedSelectedSerialNumber = '';

    public $confirmDelete = false;
    public $productToDelete;
    
    protected $updatesQueryString = ['search', 'filterStatus'];

 
 
    public $isEditModalOpen = '';
    public function updatedSelectedSerialNumber($value)
    { 
    if ($value == 'Not Available') {
        $this->editGovernDetails['serial_number'] = 'Not Available';
    }
    }


    
   //for edit
   public $editGovernDetails = [
    'id' => null,
    'order_id' => null,
    'po_number' => null,
    'name' => null,
    'payment_date' => null,
    'process_date' => null,
    'title' => null,
    'total' => null,
    'payment' => null,
    'payment_method' => null,
    'payment_balance' => null,
    'payment_status' => null,
    'remarks' => null,
    'document_status' => null,
    'status' => null,
    'isActive' => null,
];

//for editing
 protected $rules = [
    'editGovernDetails.id' => 'required|string|max:20',
    'editGovernDetails.order_id' => 'required|string|max:20',
    'editGovernDetails.po_number' => 'nullable|string|max:50',
    'editGovernDetails.name' => 'required|string|max:100',
    'editGovernDetails.payment_date' => 'required|date',
    'editGovernDetails.process_date' => 'nullable|date',
    'editGovernDetails.title' => 'nullable|string|max:100',
    'editGovernDetails.total' => 'required|numeric|min:0',
    'editGovernDetails.payment' => 'required|numeric|min:0',
    'editGovernDetails.payment_method' => 'required|string|max:50',
    'editGovernDetails.payment_balance' => 'required|numeric|min:0',
    'editGovernDetails.payment_status' => 'required|in:paid,unpaid,partial',
    'editGovernDetails.remarks' => 'nullable|string|max:255',
    'editGovernDetails.document_status' => 'nullable|string|max:50',
    'editGovernDetails.status' => 'nullable|string|max:50',
    'editGovernDetails.isActive' => 'required|in:0,1',
];

public function mount()
    {
        $this->data = [
            'order_id' => '',
            'po_number' => '',
            'name' => '',
            'title' => '',
            'total' => 0.00,
            'payment_date' => null,
            'process_date' => null,
            'document_status' => '',
            'payment' => 0.00,
            'payment_method' => '',
            'payment_balance' => 0.00,
            'payment_status' => 'unpaid',
            'remarks' => '',
            'status' => 'pending',
            'added_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ];

        $this->Order = Order::where('isActive', 1)
            ->where('customer_type', 'Government')
            ->get()
            ->filter(function ($order) {
                $latestPayment = payment::where('order_id', $order->order_id)
                    ->latest('id')
                    ->first();

                if (!$latestPayment) {
                    return true; // No payment yet
                }

                return strtolower($latestPayment->payment_status) !== 'paid' || floatval($latestPayment->balance) > 0.00;
            })
            ->values();
          }

    public function updatedSelectedpayment($value)
    {
        // Payment logic depending on selected type
        if ($value === 'Downpayment') {
            $this->data['payment'] = '';
        }

        if ($value === 'Full Payment') {
            $this->data['payment'] = $this->data['total'] ?? 0;
        }

        $this->recalculateBalance();
    }

    public function updatedOrderId($value)
    {
        $order = Order::where('order_id', $value)->first();
        $latestPayment = payment::where('order_id', $value)->latest('id')->first();

        if ($order) {
            $this->data['order_id'] = $order->order_id;
            $this->data['name'] = $order->name;
            $this->data['total'] = $order->total;
            $this->data['remarks'] = $order->remarks;
            $this->data['status'] = $order->status;
        } else {
            $this->data['name'] = '';
            $this->data['title'] = '';
            $this->data['total'] = 0;
            $this->data['remarks'] = '';
            $this->data['status'] = '';
        }

        if ($latestPayment) {
            $this->data['payment_method'] = $latestPayment->payment_method;
            $this->data['payment'] = $latestPayment->amount;
            $this->data['payment_date'] = $latestPayment->payment_date ? $latestPayment->payment_date->format('Y-m-d') : null;
            $this->data['payment_balance'] = $latestPayment->balance;
            $this->data['payment_status'] = $latestPayment->payment_status;
        } else {
            $this->data['payment_method'] = '';
            $this->data['payment'] = 0;
            $this->data['payment_date'] = null;
            $this->data['payment_balance'] = 0;
            $this->data['payment_status'] = '';
        }
    }

    public function recalculateBalance()
    {
        $total = floatval($this->data['total'] ?? 0);
        $payment = floatval($this->data['payment'] ?? 0);
        $this->data['payment_balance'] = $total - $payment;
    }




//for update
public function update()
{
    $this->validate([ // Apply validation rules
        
        'editGovernDetails.id' => 'required|string|max:20',
        'editGovernDetails.order_id' => 'required|string|max:20',
        'editGovernDetails.po_number' => 'nullable|string|max:50',
        'editGovernDetails.name' => 'required|string|max:100',
        'editGovernDetails.payment_date' => 'required|date',
        'editGovernDetails.process_date' => 'nullable|date',
        'editGovernDetails.title' => 'nullable|string|max:100',
        'editGovernDetails.total' => 'required|numeric|min:0',
        'editGovernDetails.payment' => 'required|numeric|min:0',
        'editGovernDetails.payment_method' => 'required|string|max:50',
        'editGovernDetails.payment_balance' => 'required|numeric|min:0',
        'editGovernDetails.payment_status' => 'required|in:paid,unpaid,partial',
        'editGovernDetails.remarks' => 'nullable|string|max:255',
        'editGovernDetails.document_status' => 'nullable|string|max:50',
        'editGovernDetails.status' => 'nullable|string|max:50',
        'editGovernDetails.isActive' => 'required|in:0,1',
    ]);

    $governDetails = GovernPayable::find($this->editGovernDetails['id']);

    if ($governDetails) {
        $governDetails->update([
            'id' => $this->editGovernDetails['id'],
            'order_id' => $this->editGovernDetails['order_id'],
            'po_number' => $this->editGovernDetails['po_number'],
            'name' => $this->editGovernDetails['name'],
            'payment_date' => $this->editGovernDetails['payment_date'],
            'process_date' => $this->editGovernDetails['process_date'],
            'title' => $this->editGovernDetails['title'],
            'total' => $this->editGovernDetails['total'],
            'payment' => $this->editGovernDetails['payment'],
            'payment_method' => $this->editGovernDetails['payment_method'],
            'payment_balance' => $this->editGovernDetails['payment_balance'],
            'payment_status' => $this->editGovernDetails['payment_status'],
            'remarks' => $this->editGovernDetails['remarks'],
            'document_status' => $this->editGovernDetails['document_status'],
            'status' => $this->editGovernDetails['status'],
            'isActive' => $this->editGovernDetails['isActive'],
            'updated_by' => Auth::user()->username,
        ]);
        // Emit an event to refresh the table
        $this->emit('refreshTable');

        // Close the modal
        $this->isEditModalOpen = false;


        // Show a success message
        session()->flash('messageUpdate', 'Government Payable is updated successfully.');

        // Dispatch a browser event to close the modal
        $this->dispatchBrowserEvent('closeEditModal');
    } else {
        session()->flash('error', 'Product not found.');
    }
}
public function edit($id)
{
    $governDetails = GovernPayable::find($id); // Assuming this is the new model

    if ($governDetails) {
        $this->editGovernDetails = [
            'id' => $governDetails->id,
            'order_id' => $governDetails->order_id,
            'po_number' => $governDetails->po_number,
            'name' => $governDetails->name,
            'payment_date' => $governDetails->payment_date,
            'process_date' => $governDetails->process_date,
            'title' => $governDetails->title,
            'total' => $governDetails->total,
            'payment' => $governDetails->payment,
            'payment_method' => $governDetails->payment_method,
            'payment_balance' => $governDetails->payment_balance,
            'payment_status' => $governDetails->payment_status,
            'remarks' => $governDetails->remarks,
            'document_status' => $governDetails->document_status,
            'status' => $governDetails->status,
            'isActive' => $governDetails->isActive,
        ];

        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Record not found.');
    }
}


    public $step = 1;

    
    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function openModal1()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

 
   
    public function updatingSearch(){
        $this->resetPage();
    }

    public function productTypeIDFilter()
    {
        $this->resetPage();
    }



 

    public function cancelEdit(){
        $this->reset('editGovernDetails');
        $this->isEditModalOpen = false;
    }

    //  public function unblockUser($employeeNumber)
    // {
    //     if (auth()->user()->admin != 1) {
    //         session()->flash('error', 'Unauthorized action.');
    //         return;
    //     }

    //     $key = Str::lower('login:' . $employeeNumber);
    //     RateLimiter::clear($key);

    //     session()->flash('messageUpdate', 'User login attempts have been reset.');
    // }


    public function getTableQuery(){
        return User::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editGovernDetails'); // Optionally reset the editGovernDetails data
    $this->reset('updatedSelectedSerialNumber'); // Reset any other related properties
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return GovernPayable::where('isActive', 1) // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('employee_number', 'like', "%{$this->search}%")
                  ->orWhere('lastname', 'like', "%{$this->search}%")
                   ->orWhere('firstname', 'like', "%{$this->search}%")
                    ->orWhere('middlename', 'like', "%{$this->search}%")
                    ->orwhere('username', 'like', "%{$this->search}%")
                   ->orWhere('role', 'like', "%{$this->search}%");
                
              }); // Apply search filter to multiple fields
          })
          ->get(); // Fetch the records as a collection
  }
  
  // Dynamically calculate the row count based on the current filter
  public function getRowCountProperty()
  {
      return $this->getFilteredRecords()->count(); // Count the filtered records
  }
  
  
public function render()
{
    // Base query to fetch users
    $query = GovernPayable::query();

    // Apply activation filter if selected
    if ($this->filterActivation !== '') {
        $query->where('isActive', $this->filterActivation);
    } else {
        // Default behavior: Exclude inactive users
        $query->where('isActive', 1);
    }

    // Apply search filter if search term is provided
    if (!empty($this->search)) {
        $query->where(function ($q) {
            $q->where('employee_number', 'like', "%{$this->search}%")
             ->orWhere('lastname', 'like', "%{$this->search}%")
                   ->orWhere('firstname', 'like', "%{$this->search}%")
                    ->orWhere('middlename', 'like', "%{$this->search}%")
                    ->orwhere('username', 'like', "%{$this->search}%")
              ->orWhere('role', 'like', "%{$this->search}%");
        });
    }

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.govern-payable-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}


