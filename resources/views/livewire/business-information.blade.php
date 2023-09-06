<div>
    @php
        print_r($user->company ?? null);
    @endphp
    <!-- business -->
    <div class="row justify-content-md-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="p-4 mb-5 card">
                <div class="d-flex justify-content-between">
                    <div class="d-flex">
                        <div class="my-auto c-h6 fw-medium">@lang('page.account.profile.business.title')</div>
                        @if ($businessDataStatus === 'pending')
                            <!-- pending -->
                            <div class="ms-2 badge text-bg-secondary  p-xs fw-semibold my-auto">
                                Verification pending
                            </div>
                        @elseif ($businessDataStatus === 'verified')
                            <!-- verified -->
                            <div class="ms-2 badge py-1 ps-1 pe-2 my-auto p-xs rounded fw-semibold verified-badge">
                                <svg class="icon-feather me-1 icon-badge-verified">
                                    <use xlink:href="{{asset('template/assets/icons/feather/feather-sprite.svg#check-circle')}}"></use>
                                </svg>
                                    Verified
                            </div>
                        @elseif ($businessDataStatus === 'unverified')
                            <!-- unverified -->
                            <span class="ms-2 badge py-1 ps-1 pe-2 my-auto p-xs rounded fw-semibold unverified-badge">
                                <svg class="icon-feather me-1 icon-badge-unverified">
                                    <use xlink:href="{{asset('template/assets/icons/feather/feather-sprite.svg#x-circle')}}"></use>
                                </svg>
                                    Unverified
                            </span>
                        @endif
                    </div>
                    <x-v2.button
                        :componentId="'editCompanyButton'"
                        :buttonType="'edit'"
                        :buttonWording="['normal' => __('page.account.profile.business.editButton'), 'clicked' => __('page.account.profile.business.editButton')]"
                    />
                </div>
                <hr>
                <div class="p-0 card-body">
                    <form id="business-form">
                        <div class="form-check form-switch form-switch-lg mb-3" wire:ignore>
                            <input class="form-check-input" type="checkbox" role="switch" id="check-account">
                            <label class="form-check-label" for="check-account">
                                <div class="p-sm fw-medium text-black"> @lang('page.account.profile.business.toggleTitle')</div>
                                <div class="p-sm text-gray">@lang('page.account.profile.business.toggleDescription')</div>
                            </label>
                        </div>
                        <div id="business-data" class="d-none" wire:ignore.self>
                            @if ($businessDataStatus === 'unfilled')
                                <div id="business-des" class="hosting-note p-3 rounded mb-3">
                                    <div class="d-flex justify-content-between">
                                        <svg class="icon-feather me-2">
                                            <use xlink:href="{{asset('template')}}/assets/icons/feather/feather-sprite.svg#info">
                                            </use>
                                        </svg>
                                        <div class="p-sm">
                                            @lang('page.account.profile.business.notes')
                                        </div>
                                        <svg id="hide-des" class="icon-feather ms-2" style="cursor: pointer;">
                                            <use xlink:href="{{asset('template')}}/assets/icons/feather/feather-sprite.svg#x">
                                            </use>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="company_name" class="form-label p-sm fw-medium text-gray">
                                    @lang('page.contact.information.form.label.companyName')<span class="text-danger ms-1">*</span>
                                </label>
                                <x-v2.input
                                    :componentId="'companyInputComponent'"
                                    :defaultInputValue="$name_companies"
                                    :placeHolder="__('page.contact.information.form.placeholder.companyName')" 
                                    :inputType="'text'"
                                />
                                <x-v2.input-error 
                                    :componentId="'errorCompanyInput'"
                                    :validationVariableName="'name_companies'"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label p-sm fw-medium text-gray">
                                    @lang('page.contact.information.form.label.country')<span class="text-danger ms-1">*</span>
                                </label>
                                <x-v2.country-select
                                    :componentId="'countryInputComponent'"
                                    :rules="['required']"
                                    :feature="['enableSearch' => true]"
                                    :countryList="$countryList" 
                                    :defaultCountryId="$countries_id"
                                />
                                <x-v2.input-error 
                                    :componentId="'errorCountryInput'"
                                    :validationVariableName="'countries_id'"
                                />
                            </div>
                            <div class="mb-3" id="UEN" wire:ignore.self>
                                <label for="UEN" class="form-label p-sm fw-medium text-gray">
                                    @lang('page.contact.information.form.label.uen')<span class="text-danger ms-1">*</span>
                                </label>
                                <x-v2.input
                                    :componentId="'uenInputComponent'"
                                    :defaultInputValue="$uen"
                                    :placeHolder="__('page.contact.information.form.placeholder.uen')" 
                                    :inputType="'text'"
                                    :validationType="'uen'"
                                />
                                <x-v2.input-error 
                                    :componentId="'errorUenInput'"
                                    :validationVariableName="'uen'"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label p-sm fw-medium text-gray">
                                    @lang('page.contact.information.form.label.sector')<span class="text-danger ms-1">*</span>
                                </label>
                                <x-v2.input-select
                                    :componentId="'businessSectorInputComponent'"
                                    :rules="['required']"
                                    :inputList="$businessSectorList" 
                                    :placeHolder="__('page.contact.information.form.label.sector')"
                                    :inputTable="['id' => 'id_business_sectors', 'name' => 'description']"
                                    :defaultInputId="$business_sectors_id"
                                />
                                <x-v2.input-error 
                                    :componentId="'errorBusinessSector'"
                                    :validationVariableName="'business_sectors_id'"
                                />
                            </div>
                            <div class="mb-5">
                                <label class="form-label p-sm fw-medium text-gray">
                                    @lang('page.contact.information.form.label.size')<span class="text-danger ms-1">*</span>
                                </label>
                                <x-v2.input-select
                                    :componentId="'companySizeInputComponent'"
                                    :rules="['required']"
                                    :inputList="$companySizeList" 
                                    :placeHolder="__('page.contact.information.form.placeholder.size')"
                                    :inputTable="['id' => 'id_company_sizes', 'name' => 'capacity_company_sizes', 'defaultWording' => 'employees']"
                                    :defaultInputId="$company_sizes_id"
                                />
                                <x-v2.input-error 
                                    :componentId="'errorCompanySize'"
                                    :validationVariableName="'company_sizes_id'"
                                />
                            </div>
                            <div class="text-md-start">
                                <x-v2.button
                                    :componentId="'cancelCompanyButton'"
                                    :buttonType="'cancel'"
                                    :buttonWording="['normal' => __('messages.button.cancel'), 'clicked' => __('messages.button.cancel')]"
                                    :callModal="'cancel-business'"
                                />
                                <!-- submit -->
                                <x-v2.button
                                    :componentId="'saveCompanyButton'"
                                    :buttonType="'save'"
                                    :buttonWording="['normal' => __('messages.button.submit'), 'clicked' => __('messages.button.submitting')]"
                                />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cancel -->
    <div class="modal fade" id="cancel-business" tabindex="-1" aria-labelledby="remove" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="p-4 text-center modal-content">
                <svg class="modal-icon-danger mb-3 mx-auto" style="width: 32px !important; height: 32px !important;">
                    <use id="iconToggle"
                        href="{{asset('template')}}/assets/icons/feather/feather-sprite.svg#alert-triangle"></use>
                </svg>
                <div class="mb-3 p-lg fw-medium">@lang('page.account.profile.modal.cancel_update.title')</div>
                <div class="mb-5 p-sm-50">
                    If you close the page, the data filled in this section will not be saved. Are you sure close this page?
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-border border-gray p-sm me-2"
                        data-coreui-dismiss="modal">@lang('messages.button.cancel')</button>
                    <button data-coreui-dismiss="modal" type="button"
                        class="btn btn-red p-sm" style="background-color: #EF4444;" id='companyButtonClose'>@lang('messages.button.close')</button>
                </div>
            </div>
        </div>
    </div>

    <script wire:ignore>
        class {{$jsClassName}}{
            constructor(){
                this.listCountryForUen = @json($config['enableUenCountry']);
                this.uenHeader = document.querySelector('#UEN');
                this.checkAccount = document.querySelector('#check-account');
                this.businessData = document.querySelector('#business-data');
                this.companyButtonClose = document.querySelector('#companyButtonClose');

                this.businessDataStatus = "{{$businessDataStatus}}";

                //get data from back-end
                document.addEventListener("DOMContentLoaded", () => {
                    Livewire.hook('message.processed', (message, component) => {
                        //ignore undefined and also if not same (timing problem)
                        if(component.get('businessDataStatus') && component.get('businessDataStatus') != this.businessDataStatus){
                            this.businessDataStatus = component.get('businessDataStatus');
                            // this.init(); //back-end only for this
                        }
                    });
                });

                //to close and open a form
                this.checkAccount.addEventListener('change', event => {
                    if (this.checkAccount.checked) {
                        this.businessData.classList.remove('d-none');
                    } else {
                        this.businessData.classList.add('d-none');
                    }
                });

                //listener for uen check
                countryInputComponent.getListener().inputList.forEach(countryLink => {
                    countryLink.addEventListener('click', event => {
                        let aElement = event.currentTarget;
                        this.checkCountryForUen({'countryName': aElement.dataset.name});
                    })
                });

                //when click save button
                saveCompanyButton.getListener().buttonClick.addEventListener('click', event => {
                    this.storeCompanyData();
                });

                //if edit business clicked
                editCompanyButton.getListener().buttonClick.addEventListener('click', event => {
                    this.updateForm({'status': 'edit'});
                });

                //for livewire dispatch on success
                window.addEventListener('saveFormData', event => {
                    this.updateForm({'status': 'success'});
                });

                //for livewire dispatch on failed
                window.addEventListener('failedFormData', event => {
                    this.updateForm({'status': 'failed'});
                });

                //on cancel
                this.companyButtonClose.addEventListener('click', event => {
                    this.updateForm({'status': 'cancel'});
                });

                this.init();
            }

            updateForm(data){
                switch (this.businessDataStatus) {
                    case 'pending':
                        if(data.status === 'success'){
                            this.centralTask(['saveForm', 'disableForm', 'hideButtonForm', 'showEditBusinessButton', 'resetFormError']);
                        } else if(data.status === 'failed'){
                            this.centralTask(['unclickSaveButton', 'resetFormError']);
                        } else if(data.status === 'cancel'){
                            this.centralTask(['resetFormError', 'resetForm', 'disableForm', 'hideButtonForm', 'showEditBusinessButton']);
                        } else if(data.status === 'edit'){
                            this.centralTask(['enableForm', 'disableChangeBusinessAccount', 'enableButtonForm', 'hideEditBusinessButton']);
                        }
                    break;
                    case 'verified':
                        if(data.status === 'success'){
                            this.centralTask(['saveForm', 'disableForm', 'hideButtonForm', 'showEditBusinessButton', 'resetFormError']);
                        } else if(data.status === 'failed'){
                            this.centralTask(['unclickSaveButton', 'resetFormError']);
                        } else if(data.status === 'cancel'){
                            this.centralTask(['resetFormError', 'resetForm', 'disableForm', 'hideButtonForm', 'showEditBusinessButton']);
                        } else if(data.status === 'edit'){
                            this.centralTask(['enableForm', 'disableChangeBusinessAccount', 'enableButtonForm', 'hideEditBusinessButton']);
                        }
                    break;
                    case 'unverified':
                        if(data.status === 'success'){
                            this.centralTask(['saveForm', 'disableForm', 'hideButtonForm', 'disableChangeBusinessAccount', 'resetFormError']);
                        } else if(data.status === 'failed'){
                            this.centralTask(['unclickSaveButton', 'resetFormError']);
                        } else if(data.status === 'cancel'){
                            this.centralTask(['resetFormError', 'resetForm', 'uncheckChangeBusinessAcc']);
                        } else if(data.status === 'edit'){
                            this.centralTask(['enableForm', 'disableChangeBusinessAccount', 'enableButtonForm', 'hideEditBusinessButton']);
                        }
                    break;
                    case 'unfilled':
                    if(data.status === 'success'){
                            this.centralTask(['saveForm', 'disableForm', 'hideButtonForm', 'disableChangeBusinessAccount', 'resetFormError']);
                        } else if(data.status === 'failed'){
                            this.centralTask(['unclickSaveButton', 'resetFormError']);
                        } else if(data.status === 'cancel'){
                            this.centralTask(['resetFormError', 'resetForm', 'uncheckChangeBusinessAcc']);
                        }
                    break;
                }
            }

            //bunch of command group togethers
            centralTask(data){
                data.forEach(element => {
                    switch (element) {
                        case 'showEditBusinessButton':
                            editCompanyButton.htmlBehavior({'hidden': false});
                            editCompanyButton.buttonSwitch({'clicked': false});
                        break;
                        case 'hideEditBusinessButton':
                            editCompanyButton.htmlBehavior({'hidden': true});
                            editCompanyButton.buttonSwitch({'clicked': false});
                        break;
                        case 'disableForm':
                            companyInputComponent.htmlBehavior({'disabled': true});
                            countryInputComponent.htmlBehavior({'disabled': true});
                            uenInputComponent.htmlBehavior({'disabled': true});
                            businessSectorInputComponent.htmlBehavior({'disabled': true});
                            companySizeInputComponent.htmlBehavior({'disabled': true});
                        break;
                        case 'resetForm':
                            companyInputComponent.resetValue();
                            countryInputComponent.resetValue();
                            uenInputComponent.resetValue();
                            businessSectorInputComponent.resetValue();
                            companySizeInputComponent.resetValue();
                        break;
                        case 'resetFormError':
                            errorCompanyInput.resetError();
                            errorCountryInput.resetError();
                            errorUenInput.resetError();
                            errorBusinessSector.resetError();
                            errorCompanySize.resetError();
                        break;
                        case 'saveForm':
                            companyInputComponent.saveValue();
                            countryInputComponent.saveValue();
                            uenInputComponent.saveValue();
                            businessSectorInputComponent.saveValue();
                            companySizeInputComponent.saveValue();
                        break;
                        case 'hideButtonForm':
                            saveCompanyButton.buttonSwitch({'clicked': false});
                            saveCompanyButton.htmlBehavior({'hidden': true});
                            cancelCompanyButton.buttonSwitch({'clicked': false});
                            cancelCompanyButton.htmlBehavior({'hidden': true});
                        break;
                        case 'enableButtonForm':
                            saveCompanyButton.buttonSwitch({'clicked': false});
                            saveCompanyButton.htmlBehavior({'hidden': false});
                            cancelCompanyButton.buttonSwitch({'clicked': false});
                            cancelCompanyButton.htmlBehavior({'hidden': false});
                        break;
                        case 'clickChangeBusinessAccount':
                            this.checkAccount.click();
                        break;
                        case 'disableChangeBusinessAccount':
                            this.checkAccount.disabled = true;
                            this.businessData.classList.remove('d-none');
                        break;
                        case 'unclickSaveButton':
                            saveCompanyButton.buttonSwitch({'clicked': false});
                        break;
                        case 'enableForm':
                            companyInputComponent.htmlBehavior({'disabled': false});
                            countryInputComponent.htmlBehavior({'disabled': false});
                            uenInputComponent.htmlBehavior({'disabled': false});
                            businessSectorInputComponent.htmlBehavior({'disabled': false});
                            companySizeInputComponent.htmlBehavior({'disabled': false});
                        break;
                        case 'hideForm':
                            companyInputComponent.htmlBehavior({'hidden': true});
                            countryInputComponent.htmlBehavior({'hidden': true});
                            uenInputComponent.htmlBehavior({'hidden': true});
                            businessSectorInputComponent.htmlBehavior({'hidden': true});
                            companySizeInputComponent.htmlBehavior({'hidden': true});
                        break;
                        case 'uncheckChangeBusinessAcc':
                            this.checkAccount.click();
                            this.businessData.classList.add('d-none');
                        break;
                        case 'callStickyAlert':
                            // If Livewire is already loaded, emit the event immediately
                            if (window.Livewire) {
                                Livewire.emit('callStickyAlert');
                            } else {
                                // If Livewire is not yet loaded, wait for the livewire:load event
                                document.addEventListener('livewire:load', function () {
                                    Livewire.emit('callStickyAlert');
                                });
                            }
                        break;
                    }
                });

                //check country for the uen
                this.checkCountryForUen({'countryName': countryInputComponent.getValue().desc});
            }

            //disable uen according to defaultCountry
            checkCountryForUen(data){
                if (this.listCountryForUen.includes(data.countryName.toUpperCase())) {
                    uenInputComponent.htmlBehavior({'hidden': false});
                    this.uenHeader.hidden = false;
                } else {
                    uenInputComponent.setValue({'inputValue': ""});
                    uenInputComponent.htmlBehavior({'hidden': true});
                    this.uenHeader.hidden = true;
                }
            }

            storeCompanyData(){
                const formData = {
                    'name_companies': companyInputComponent.getValue().data,
                    'countries_id': countryInputComponent.getValue().data,
                    'uen': uenInputComponent.getValue().data,
                    'business_sectors_id': businessSectorInputComponent.getValue().data,
                    'company_sizes_id': companySizeInputComponent.getValue().data
                };

                Livewire.emit('storeCompanyData', formData);
            }

            //first initialization
            init(){
                this.checkCountryForUen({'countryName': countryInputComponent.getValue().desc});
                //unverified have same behavior as null or else for now
                if(this.businessDataStatus === 'unfilled'){
                    this.centralTask(['hideEditBusinessButton']);
                } else if(this.businessDataStatus === 'unverified'){
                    this.centralTask(['callStickyAlert', 'clickChangeBusinessAccount', 'showEditBusinessButton', 'disableChangeBusinessAccount', 'hideButtonForm', 'disableForm']);
                } else if(this.businessDataStatus === 'pending'){
                    this.centralTask(['callStickyAlert', 'clickChangeBusinessAccount', 'hideEditBusinessButton', 'disableChangeBusinessAccount', 'hideButtonForm', 'disableForm']);
                } else if(this.businessDataStatus === 'verified'){
                    this.centralTask(['clickChangeBusinessAccount', 'showEditBusinessButton', 'disableForm', 'hideButtonForm', 'disableChangeBusinessAccount']);
                }
            }
        }

        const {{$jsInstanceName}} = new {{$jsClassName}}();
    </script>
</div>
