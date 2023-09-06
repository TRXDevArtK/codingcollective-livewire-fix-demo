@props([
    'componentId', //component Id for parent access
    'defaultInputValue', //default input on init
    'placeHolder', //input placeholder
    'inputType', //input type
    'validationType' => null, //type of validation (JS check)
    'wireIgnore' => true
])

{{-- html id init with unique identifier --}}
@php
    $uniqueIdentifier = mt_rand();
    $id = [
        'inputDiv' => 'inputDiv'
    ];

    $randomJavascriptClassName = $componentId.$uniqueIdentifier;

    //append unique identifier to html id
    foreach ($id as $key => $value) {
        $id[$key] = $value . '-' . $uniqueIdentifier;
    }
@endphp

<div {{ $wireIgnore ? 'wire:ignore' : '' }}>
    <input id="{{$id['inputDiv']}}" type="{{$inputType}}" 
    class="form-control p-sm shadow-sm" placeholder="{{$placeHolder}}" value="{{$defaultInputValue}}">
    <script>
        class {{$randomJavascriptClassName}}{
            constructor() {
                //default value
                this.defaultValue = "{{$defaultInputValue ?? null}}" ?? null;

                //validationType
                this.validationType = "{{$validationType}}" ?? null;

                //rules
                this.variableRules = @json($rules ?? null);

                @if (!empty($defaultInputValue))
                    this.isValidate = true;
                @else
                    this.isValidate = false;
                @endif

                this.validationMessage = "";

                //querySelector
                this.inputDiv = document.querySelector(`#{{$id['inputDiv']}}`);

                //variable
                this.inputValue = this.defaultValue;

                //svg error
                this.svgError = `<svg class="icon-danger">
                        <use href="{{asset('template/assets/icons/feather/feather-sprite.svg#alert-triangle')}}"></use>
                    </svg>`

                //set validation
                if(this.validationType === 'company'){
                    this.inputDiv.setAttribute('x-on:input', `inputValidate($event, 'string', 125)`);
                } else if(this.validationType === 'uen'){
                    this.inputDiv.setAttribute('x-on:input', `inputValidate($event, 'uen', 10)`);
                }

                //event listener
                this.inputDiv.addEventListener('change', event => {
                    this.setValue({'inputValue': event.target.value});
                });

                this.inputDiv.addEventListener('input', event => {
                    this.setValue({'inputValue': event.target.value});
                });
            }

            //reset input value
            resetValue(){
                this.inputValue = this.defaultValue;
                this.setValue({'inputValue': this.defaultValue})
            }

            //set input value (ui only)
            setValue(data){
                this.inputValue = data.inputValue;
                this.inputDiv.value = data.inputValue;
            }

            getValue(){
                const result = {'data': this.inputValue, 'desc': ""};
                return result;
            }

            getListener(){
                const result = {'inputListener': this.inputDiv};
                return result;
            }

            //save current value as default value
            saveValue(){
                this.defaultValue = this.inputValue;
            }

            htmlBehavior(data){//true / false
                if(data.disabled === true || data.disabled === false){
                    this.inputDiv.disabled = data.disabled;
                }

                if(data.hidden === true || data.hidden === false){
                    this.inputDiv.hidden = data.hidden;
                }
            }
        }

        const {{$componentId}} = new {{$randomJavascriptClassName}}();
    </script>
</div>