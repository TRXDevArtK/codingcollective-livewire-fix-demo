@props([
    'componentId', //component Id for parent access
    'rules', //rules like ['required', 'nullable']
    'countryList' => array(), //list of country
    'feature' => ['enableSearch' => true], //bunch of feature pack
    'defaultCountryId', //a default countryId
    'wireIgnore' => true
])

{{-- html id init with unique identifier --}}
@php
    $uniqueIdentifier = mt_rand();
    $id = [
        'data' => 'data',
        'countryClick' => 'countryClick',
        'countryDiv' => 'countryDiv',
        'countryFlag' => 'countryFlag',
        'codeNumber' => 'codeNumber',
        'dropdownCountry' => 'dropdownCountry',
        'searchBox' => 'searchBox',
        'countryList' => 'countryList',
        'countryName' => 'countryName'
    ];

    $randomJavascriptClassName = $componentId.$uniqueIdentifier;

    //append unique identifier
    foreach ($id as $key => $value) {
        $id[$key] = $value . '-' . $uniqueIdentifier;
    }
@endphp

{{-- some data processing --}}
@php
    $defaultCountry = [
        'symbol_countries' => null,
        'name_countries' => null,
        'id_countries' => null
    ];

    foreach ($countryList as $countryKey => $countryValue){
        if ($countryValue['id_countries'] == intval($defaultCountryId)){
            $defaultCountry = [
                'symbol_countries' => strtolower($countryValue['symbol_countries']),
                'name_countries' => $countryValue['name_countries'],
                'id_countries' => $countryValue['id_countries']
            ];
            break;
        }
    }
@endphp

<div {{ $wireIgnore ? 'wire:ignore' : '' }}>
    <div class="dropdown w-100 hierarchy-select shadow-sm rounded" id={{$id['countryDiv']}}>
        <button id="{{$id['countryClick']}}"
            class="btn border-gray w-100 d-flex justify-content-between" type="button"
            data-coreui-toggle="dropdown" aria-expanded="false">
            <div id={{$id['data']}} class="my-auto">
                <svg class="icon-flags">
                    <use id="{{$id['countryFlag']}}" xlink:href="{{asset('template/assets/flag/flag.svg#'.strtolower($defaultCountry['symbol_countries']))}}"></use>
                </svg>
                <span id={{$id['codeNumber']}} class="ms-1 me-1 p-sm">{{$defaultCountry['name_countries']}}</span>
            </div>
            <div class="my-auto">
                <img src="{{asset('template/assets/img/custom/select-icon.svg')}}" alt="" class="img-fluid">
            </div>
        </button>
        <div class="dropdown-menu w-100" id="{{$id['dropdownCountry']}}">
            @if (!empty($feature['enableSearch']) && $feature['enableSearch'] == true)
                <div class="hs-searchbox">
                    <input type="text" id="{{$id['searchBox']}}" class="form-control" autocomplete="on">
                </div>
            @endif
            <div class="hs-menu-inner force-scroll" id="{{$id['countryList']}}">
                @foreach ($countryList as $countryId => $countryValue)
                <li>
                    <a class="dropdown-item p-sm justify-content-between" data-name="{{ucfirst($countryValue['name_countries'])}}" data-symbol="{{$countryValue['symbol_countries']}}" data-id="{{$countryValue['id_countries']}}">
                        <svg class="icon me-2 icon-flags">
                            <use
                                xlink:href="{{asset('template')}}/assets/flag/flag.svg#{{strtolower($countryValue['symbol_countries'])}}">
                            </use>
                        </svg>
                        <span class="text-gray" id="{{$id['countryName']."-".$countryId}}">{{ucfirst($countryValue['name_countries'])}}</span>
                    </a>
                </li>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        class {{$randomJavascriptClassName}}{
            constructor(){
                //default value
                this.defaultCountryNameVar = "{{$defaultCountry['name_countries'] ?? null}}" ?? null;
                this.defaultCountryIdVar = "{{$defaultCountry['id_countries'] ?? null}}" ?? null;
                this.defaultCountrySymbol = "{{$defaultCountry['symbol_countries'] ?? null}}" ?? null;
                
                //variable
                this.countryNameValue = this.defaultCountryNameVar;
                this.countryIdValue = this.defaultCountryIdVar;
                this.countryFlagUrl = "{{asset('template/assets/flag/flag.svg#')}}";
                this.countrySymbol = this.defaultCountrySymbol;

                //querySelector
                this.inputDiv = document.querySelector(`#{{$id['countryDiv']}}`);
                this.countryClick = document.querySelector(`#{{$id['countryClick']}}`);
                this.countryList = document.querySelectorAll(`#{{$id['countryList']}} a`);

                //NOTE: string of country
                this.codeNumberDiv = document.querySelector(`#{{$id['codeNumber']}}`);
                this.countryFlag = document.querySelector(`#{{$id['countryFlag']}}`);

                //svg error
                this.svgError = `<svg class="icon-danger">
                        <use href="{{asset('template/assets/icons/feather/feather-sprite.svg#alert-triangle')}}"></use>
                    </svg>`

                // Add event listeners to each country link
                this.countryList.forEach(countryLink => {
                    countryLink.addEventListener('click', event => {
                        //lock to just <a> instead of specific click
                        let aElement = event.currentTarget;
                        
                        // Access the data attributes
                        this.countryNameValue = aElement.dataset.name;
                        this.countryIdValue = aElement.dataset.id;
                        this.countrySymbol = aElement.dataset.symbol;

                        this.setValue({
                            'countryText': aElement.dataset.name, 
                            'countrySymbol': this.countryFlagUrl+aElement.dataset.symbol.toLowerCase()
                        });
                    });
                });

                @if (!empty($feature['enableSearch']) && $feature['enableSearch'] == true)
                    //country search algorithm, don't touch
                    if (document.querySelectorAll("#{{$id['countryDiv']}}")) {
                        const countries = document.querySelectorAll("#{{$id['countryDiv']}}");
                        countries.forEach((country, index) => {
                            const button = country.querySelector("#{{$id['countryClick']}}");
                            let search = country.querySelector("#{{$id['searchBox']}}");
                            let list = country.querySelector("#{{$id['countryList']}}").getElementsByTagName("a");
                            button.addEventListener("click", function (e) {
                                search.focus();
                            });
                            //country search feature
                            search.addEventListener("keyup", function (e) {
                                let value = search.value.toUpperCase();
                                for (let r = 0; r < list.length; r++) {
                                    // Get the text content of the current element
                                    const elementText = list[r].textContent || list[r].innerText;

                                    // Convert the text content to uppercase
                                    const textUpperCase = elementText.toUpperCase();

                                    // Check if the uppercase text contains the specified value
                                    const containsValue = textUpperCase.indexOf(value) > -1;

                                    // Set the display style of the element based on the condition
                                    if (containsValue) {
                                        // If the condition is true, display the element
                                        list[r].style.display = "";
                                    } else {
                                        // If the condition is false, hide the element
                                        list[r].style.display = "none";
                                    }
                                }
                            });
                        });
                    }
                @endif
            }

            //reset to defaults
            resetValue(){
                this.countryNameValue = this.defaultCountryNameVar;
                this.countryIdValue = this.defaultCountryIdVar;

                this.setValue({
                    'countryText': this.defaultCountryNameVar,
                    'countrySymbol': this.countryFlagUrl+this.defaultCountrySymbol.toLowerCase()
                });
            }

            //save current value as default value
            saveValue(){
                this.defaultCountryNameVar = this.countryNameValue;
                this.defaultCountryIdVar = this.countryIdValue;
                this.defaultCountrySymbol = this.countrySymbol;
            }

            getValue(){
                const result = {'data': this.countryIdValue, 'desc': this.countryNameValue};
                return result;
            }

            getListener(){
                const result = {'inputClick': this.countryClick, 'inputList': this.countryList};
                return result;
            }

            htmlBehavior(data){//true / false
                if(data.disabled === true || data.disabled === false){
                    this.countryClick.disabled = data.disabled;
                }

                if(data.hidden === true || data.hidden === false){
                    this.inputDiv.hidden = data.hidden;
                }
            }

            //set country value (ui only)
            setValue(data){
                this.codeNumberDiv.textContent = data.countryText;
                this.countryFlag.setAttribute("xlink:href", data.countrySymbol);
            }
        }

        const {{$componentId}} = new {{$randomJavascriptClassName}}();
    </script>
</div>
