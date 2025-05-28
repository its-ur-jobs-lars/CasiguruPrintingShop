<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
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
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Changepassword extends Component
{
    
    // In ProfileUser.php
    protected $listeners = ['passwordChanged'];
    public function render()
    {
        return view('livewire.changepassword');
    }

    public function passwordChanged($timestamp)
{
    $this->lastPasswordChange = \Carbon\Carbon::parse($timestamp);
}

     public $isChangePasswordModalOpen = false;
    public $modalUserName = '';
    public $passwordData = [
        'current_password' => '',
        'new_password' => '',
        'confirm_password' => '',
     ];

public function openChangePasswordModal()
{
    $user = Auth::user();
    $this->modalUserName = $user ? $user->username : '';
    $this->passwordData['new_password'] = '';
    $this->passwordData['confirm_password'] = '';
    $this->isChangePasswordModalOpen = true;
}

public function closeChangePasswordModal()
{
    $this->isChangePasswordModalOpen = false;
    $this->reset('passwordData');
}


    public function changePassword()
    {
        $this->validate([
              'passwordData.current_password' => 'required',
              'passwordData.new_password' => 'required|min:8',
              'passwordData.confirm_password' => 'required|same:passwordData.new_password',
        ]);

        $user = Auth::user();

        if ($user) {
            $user->update([
                'password' => Hash::make($this->passwordData['new_password']),
                // 'password_changed_at' => now(), // Uncomment if you have this column
            ]);
            session()->flash('messageInsert', 'Password changed successfully!');
        } else {
            session()->flash('errorInsert', 'Unable to retrieve your user record.');
        }

            // Emit event to parent
        $this->emitUp('passwordChanged', now());

        $this->closeChangePasswordModal();
    }


}
