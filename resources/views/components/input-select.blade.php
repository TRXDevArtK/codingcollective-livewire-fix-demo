@props([
    'componentId', //component Id for parent access
    'inputList' => array(), //list of input
    'inputTable', //example : ['id' => 'id_tablewhat', 'name' => 'description_of_column'],
    'feature' => ['enableSearch' => false], //bunch of feature pack
    'placeHolder' => 'default placeholder',
    'defaultInputId', //a default inputId
    'wireIgnore' => true,
])

{{-- html id init with unique identifier --}}
@php
    $uniqueIdentifier = mt_rand();
    $id = [
        'data' => 'data',
        'inputClick' => 'inputClick',
        'inputDiv' => 'inputDiv',
        'inputValue' => 'inputValue',
        'dropdownInput' => 'dropdownInput',
        'searchBox' => 'searchBox',
        'inputList' => 'inputList',
        'inputName' => 'inputName'
    ];

    $randomJavascriptClassName = $componentId.$uniqueIdentifier;

    //append unique identifier
    foreach ($id as $key => $value) {
        $id[$key] = $value . '-' . $uniqueIdentifier;
    }
@endphp

{{-- some data processing --}}
@php
    $defaultInput = [];
    if(!empty($defaultInputId)){
        foreach ($inputList as $inputKey => $inputValue){
            //check if object
            if(is_object($inputValue)){
                $inputValue = (array)$inputValue;
            }
            if ($inputValue[$inputTable['id']] == intval($defaultInputId)){
                $defaultInput = [
                    $inputTable['id'] => strtolower($inputValue[$inputTable['id']]),
                    $inputTable['name'] => $inputValue[$inputTable['name']],
                ];
                break;
            }
        }
    }
@endphp

<div {{ $wireIgnore ? 'wire:ignore' : '' }}>
    <div class="dropdown w-100 hierarchy-select shadow-sm rounded" id={{$id['inputDiv']}}>
        <button id="{{$id['inputClick']}}"
            class="btn border-gray w-100 d-flex justify-content-between" type="button"
            data-coreui-toggle="dropdown" aria-expanded="false">
            <div id={{$id['data']}} class="my-auto">
                @if (!empty($defaultInput))
                    <div id="{{$id['inputValue']}}" class="p-sm my-auto">                             
                        {{$defaultInput[$inputTable['name']]}}
                    </div>
                @else
                    <div id="{{$id['inputValue']}}" class="p-sm my-auto c-placeholder">
                        {{$placeHolder}}
                    </div>
                @endif
            </div>
            <div class="my-auto">
                <img src="{{asset('template/assets/img/custom/select-icon.svg')}}" alt="" class="img-fluid">
            </div>
        </button>
        <div class="dropdown-menu w-100" id="{{$id['dropdownInput']}}">
            @if (!empty($feature['enableSearch']) && $feature['enableSearch'] == true)
                <div class="hs-searchbox">
                    <input type="text" id="{{$id['searchBox']}}" class="form-control" autocomplete="on">
                </div>
            @endif
            <div class="hs-menu-inner force-scroll" id="{{$id['inputList']}}">
                @foreach ($inputList as $inputId => $inputValue)
                @php
                    if(is_object($inputValue)){
                        $inputValue = (array)$inputValue;
                    }
                @endphp
                <li>
                    <a class="dropdown-item p-sm" data-id="{{$inputValue[$inputTable['id']]}}" data-name="{{$inputValue[$inputTable['name']]}}">
                        {{$inputValue[$inputTable['name']]}}
                        @if (!empty($inputTable['defaultWording']))
                            {{$inputTable['defaultWording']}}
                        @endif
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
                this.defaultInputNameVar = "{{$defaultInput[$inputTable['name']] ?? null}}" ?? null;
                this.defaultInputIdVar = "{{$defaultInput[$inputTable['id']] ?? null}}" ?? null;
                this.defaultPlaceHolder = "{{$placeHolder ?? null}}" ?? null;

                //tablelist
                this.defaultWording = `{{$inputTable['defaultWording'] ?? ""}}`;
                
                //variable
                this.inputNameValue = this.defaultInputNameVar;
                this.inputIdValue = this.defaultInputIdVar;

                //querySelector
                this.inputDiv = document.querySelector(`#{{$id['inputDiv']}}`);
                this.inputClick = document.querySelector(`#{{$id['inputClick']}}`);
                this.inputList = document.querySelectorAll(`#{{$id['inputList']}} a`);
                this.inputValueDiv = document.querySelector("#{{$id['inputValue']}}");

                //svg error
                this.svgError = `<svg class="icon-danger">
                        <use href="{{asset('template/assets/icons/feather/feather-sprite.svg#alert-triangle')}}"></use>
                    </svg>`

                // Add event listeners to each input link
                this.inputList.forEach(inputLink => {
                    inputLink.addEventListener('click', event => {
                        //lock to just <a> instead of specific click
                        let aElement = event.currentTarget;
                        
                        // Access the data attributes
                        this.inputNameValue = aElement.dataset.name;
                        this.inputIdValue = aElement.dataset.id;

                        this.setValue({
                            'inputText': aElement.dataset.name
                        });
                    });
                });

                //input search algorithm (independent)
                @if (!empty($feature['enableSearch']) && $feature['enableSearch'] == true)
                    if (document.querySelectorAll("#{{$id['inputDiv']}}")) {
                        const inputDiv = document.querySelectorAll("#{{$id['inputDiv']}}");
                        inputDiv.forEach((input, index) => {
                            const button = input.querySelector("#inputClick");
                            let search = input.querySelector("#{{$id['searchBox']}}");
                            let list = input.querySelector("#inputList").getElementsByTagName("a");
                            button.addEventListener("click", function (e) {
                                search.focus();
                            });
                            //input search feature
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
                this.inputNameValue = this.defaultInputNameVar;
                this.inputIdValue = this.defaultInputIdVar;

                if(this.defaultInputNameVar){
                    this.setValue({
                        'inputText': this.defaultInputNameVar
                    });
                } else {
                    this.setValue({
                        'inputText': this.defaultPlaceHolder,
                        'placeHolder': true
                    });
                }
            }

            //save current value as default value
            saveValue(){
                this.defaultInputNameVar = this.inputNameValue;
                this.defaultInputIdVar = this.inputIdValue;
            }

            getValue(){
                const result = {'data': this.inputIdValue, 'desc': this.inputNameValue};
                return result;
            }

            getListener(){
                const result = {'inputClick': this.inputClick, 'inputList': this.inputList};
                return result;
            }

            htmlBehavior(data){//true / false
                if(data.disabled === true || data.disabled === false){
                    this.inputClick.disabled = data.disabled;
                }

                if(data.hidden === true || data.hidden === false){
                    this.inputDiv.hidden = data.hidden;
                }
            }

            //set input value (ui only)
            setValue(data){
                this.inputValueDiv.textContent = data.inputText+" "+this.defaultWording;
                if(data.placeHolder === true){
                    this.inputValueDiv.className = "p-sm my-auto c-placeholder";
                } else {
                    this.inputValueDiv.className = "p-sm my-auto";
                }
            }
        }

        const {{$componentId}} = new {{$randomJavascriptClassName}}();
    </script>
</div>
