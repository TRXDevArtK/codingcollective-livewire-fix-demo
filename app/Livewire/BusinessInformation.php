<?php

namespace App\Http\Livewire\Account\Profile\Components;

use App\Helpers\UserPortal;
use App\Http\Traits\AlertTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use Services\CompanyServices;
use App\Http\Traits\MustValidatingTrait;
use Illuminate\Validation\ValidationException;
use Services\CountryServices;
use Services\ProfileServices;
use Services\UserServices;
use Services\ValidationTypeServices;

class BusinessInformation extends Component
{
    use AlertTrait, MustValidatingTrait;

    // this class config
    public $config = [
        'defaultCountry' => 'SINGAPORE', //
        'enableUenCountry' => ['SINGAPORE']
    ];
    //imported variable (from outside this class)
    public $phoneNumberCountryId;

    //private variable (for operations)
    private $user, $accountTypeId, $companyData;

    //inputted variable
    public $name_companies, $business_sectors_id, $company_sizes_id, $countries_id, $uen;

    //table variable (list)
    public $countryList, $accountTypeList, $businessSectorList, $companySizeList;

    //UI Conditional
    public $businessDataStatus = 'unfilled';

    //javascript class
    public $jsClassName, $jsInstanceName;

    protected $rules = [
        'name' => 'required|min:6',
        'email' => 'required|email',
    ];

    //DI
    private $companyServices, $countryServices, $userServices, $validationTypeServices, $profileServices;

    protected $listeners = ['storeCompanyData', 'callStickyAlert'];

    //run everytime livewire refresh
    public function boot(
        CompanyServices $companyServices, 
        CountryServices $countryServices,
        UserServices $userServices,
        ValidationTypeServices $validationTypeServices,
        ProfileServices $profileServices
    ){
        $this->companyServices = $companyServices;
        $this->countryServices = $countryServices;
        $this->userServices = $userServices;
        $this->validationTypeServices = $validationTypeServices;
        $this->profileServices = $profileServices;
        $this->user = Auth::user();
    }

    //only run 1x on page reloads
    public function mount(){
        $this->jsClassName = "class".mt_rand();
        $this->jsInstanceName ="instance".mt_rand();

        //inputted Variable
        $this->companyData = $this->user->company ?? null;

        //set countries as same as phonecountry per-reload page if not filled yet business/company data
        $this->countries_id = $this->phoneNumberCountryId ?? null;
        
        //check verified status
        if (!empty($this->user->company)) {
            if (!empty($this->user->company['verified_at'])) { // have verified at
                $this->businessDataStatus = 'verified';
            } else if(!empty($this->user->company) && empty($this->user->company['verified_at'])){ // have data
                $this->businessDataStatus = 'pending';
            } else {
                //unverified, unknown condition for now
                $this->businessDataStatus = 'unverified';
            }

            //set it to filled if company is there for countries
            $this->countries_id = $this->companyData['countries_id'];
        }

        // $this->callStickyAlert();
        $this->accountTypeId = $this->user->profiles['account_types_id'] ?? null;
        $this->name_companies = $this->companyData['name_companies'] ?? null;
        $this->uen = $this->companyData['uen'] ?? null;
        $this->business_sectors_id = $this->companyData['business_sectors_id'] ?? null;
        $this->company_sizes_id = $this->companyData['company_sizes_id'] ?? null;

        $this->countryList = UserPortal::getCountries()->toArray();
        $this->accountTypeList = UserPortal::getAccountTypes()->toArray();
        $this->businessSectorList = UserPortal::getBusinessSectors()->toArray();
        $this->companySizeList = UserPortal::getCompanySizes()->toArray();

        //if country null then set to default country
        if(empty($this->countries_id)){
            foreach($this->countryList as $countryKey => $countryValue){
                if(strtoupper($countryValue['name_countries']) === strtoupper($this->config['defaultCountry'])){
                    $this->countries_id = $countryValue['id_countries'];
                    break;
                }
            }
        }
    }

    private function validation(){
        $getCountryName = $this->countryServices->getCountryDataByListCountry([
            'countryId' => $this->countries_id,
            'countryList' => $this->countryList
        ]);

        $companyRules = $this->companyServices->customRules(['country' => $getCountryName['name_countries']]);

        $this->validate($companyRules);
    }

    private function populateCompanyForm($data){

        $getCountryName = strtoupper($this->countryServices->getCountryDataByListCountry([
            'countryId' => $data['countries_id'], 
            'countryList' => $this->countryList
        ])['name_countries']);

        $this->name_companies = $data['name_companies'];
        $this->business_sectors_id = !empty($data['business_sectors_id']) ? intval($data['business_sectors_id']) : null;
        $this->company_sizes_id = !empty($data['company_sizes_id']) ? intval($data['company_sizes_id']) : null;

        if($getCountryName != 'SINGAPORE'){
            $this->uen = null;
        } else {
            $this->uen = $data['uen'];
        }

        $this->countries_id = !empty($data['countries_id']) ? intval($data['countries_id']) : null;

        return [
            'users_id' => $this->user->id_users,
            'name_companies' => $this->name_companies,
            'business_sectors_id' => $this->business_sectors_id,
            'company_sizes_id' => $this->company_sizes_id,
            'countries_id' => $this->countries_id,
            'uen' => $this->uen,
            'verified_at' => null //refresh every update
        ];
    }

    private function checkIfInputIsSame($data){

        if(empty($this->user->company)){
            return false;
        }

        $dbCompanyData = [
            'users_id' => $this->user->company['users_id'],
            'name_companies' => $this->user->company['name_companies'],
            'business_sectors_id' => $this->user->company['business_sectors_id'],
            'company_sizes_id' => $this->user->company['company_sizes_id'],
            'countries_id' => $this->user->company['countries_id'],
            'uen' => $this->user->company['uen']
        ];

        if(empty(array_diff($dbCompanyData, $data))){
            return true;
        } else {
            return false;
        }
    }

    private function resetUi($data){
        if(!empty($data['status']) && $data['status'] == 'success'){
            $this->dispatchBrowserEvent('saveFormData');
        } else if(!empty($data['status']) && $data['status'] == 'failed'){
            $this->dispatchBrowserEvent('failedFormData');
        } else if(!empty($data['status']) && $data['status'] == 'cancel'){
            $this->dispatchBrowserEvent('cancelFormData');
        }
    }

    private function updateAccountTypeProfile(){
        //if it still 1 / personal, then update it
        if($this->user->profiles['account_types_id'] === 1){
            $this->profileServices->update($this->user->profiles, ['account_types_id' => 2]);
        }
    }

    public function callStickyAlert(){
        if($this->businessDataStatus === 'unverified'){
            $this->emit('stickyNotification', [
                'header' => null, 
                'body' => __('messages.business_message.unverified'), 
                'style' => 'danger',
            ]);
        } else if($this->businessDataStatus === 'pending'){
            $this->emit('stickyNotification', [
                'header' => null, 
                'body' => __('messages.business_message.pending'), 
                'style' => 'warning'
            ]);
        } else {
            $this->emit('stickyNotification', null);
        }
    }

    public function storeCompanyData($data){
        if($this->businessDataStatus === 'pending'){
            Log::info(__('security.front_end_bypass', ['ip' => Request::ip()]));
            return; // do nothing
        }

        try {
            //process
            $populatedData = $this->populateCompanyForm($data);
            $this->validation();
            $isInputSame = $this->checkIfInputIsSame($populatedData);
            if($isInputSame === false){
                $this->companyServices->saveCompany($populatedData, $this->user->company);
                $this->businessDataStatus = 'pending';
            }
            
            //if success
            $this->callStickyAlert();
            $successMessage = __('messages.update_business_info.success.header');
            $this->emit('successAlert', $successMessage);
            $this->resetUi(['status' => 'success']);
            $this->updateAccountTypeProfile();
            $this->validationTypeServices->verifyProfileStep();
        } catch (ValidationException $e) {
            //if failed on validation
            $this->resetUi(['status' => 'failed']);
            throw $e;
        } catch (\Throwable $th) {
            //if failed if there's an error
            $errorHeader = __('messages.update_business_info.error.header');
            $errorBody = $th->getMessage();
            $this->emit('errorAlert', $errorHeader, $errorBody);
            $this->resetUi(['status' => 'failed']);
            Log::error('Error in : ' . __FILE__, ['exception' => $th]);
        }
    }

    public function render()
    {
        return view('livewire.business-information');
    }

}
