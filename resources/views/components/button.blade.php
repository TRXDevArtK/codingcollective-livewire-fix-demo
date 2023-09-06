{{-- Note: mostly use for normal button that have animation or close or can be hidden
don't use this as modal button --}}

@props([
    'componentId', //component Id for parent access
    'buttonType', //can be called style, current: save,cancel,edit,close
    'buttonWording', //example: ['normal' => 'save', 'clicked' => 'Saving. . .'],
    'callModal' => '', //if you want to call modal
    'wireIgnore' => true
])

{{-- html id init with unique identifier --}}
@php
    $uniqueIdentifier = mt_rand();
    $id = [
        'buttonDiv' => 'buttonDiv'
    ];

    $randomJavascriptClassName = $componentId.$uniqueIdentifier;

    //append unique identifier to html id
    foreach ($id as $key => $value) {
        $id[$key] = $value . '-' . $uniqueIdentifier;
    }
@endphp

<span {{ $wireIgnore ? 'wire:ignore' : '' }}>
    <button type="button" id="{{$id['buttonDiv']}}">
        {{-- js generated --}}
    </button>
</span>
<script {{ $wireIgnore ? 'wire:ignore' : '' }}>
    class {{$randomJavascriptClassName}}{
        constructor(options){
            this.buttonDivId = options.buttonDiv;
            this.buttonDiv = document.querySelector(`#${options.buttonDiv}`);
            this.buttonWording = options.buttonWording;
            this.callModal = options.callModal;
            this.isButtonClicked = false;

            //init
            this.buttonSwitch({'clicked': false});

            //event listener
            this.buttonDiv.addEventListener('click', event => {
                this.buttonSwitch({'clicked': true});
            });
        }

        getListener(){
            const result = {'buttonClick': this.buttonDiv};
            return result;
        }

        isButtonClick(){
            const result = {'data': this.isButtonClicked, 'desc': ''};
            return result;
        }

        htmlBehavior(data){//true / false
            if(data.disabled === true || data.disabled === false){
                this.buttonDiv.disabled = data.disabled;
            }

            if(data.hidden === true || data.hidden === false){
                this.buttonDiv.hidden = data.hidden;
            }
        }

        buttonSwitch(data){
            //type or style
            @if ($buttonType === 'save')
                //clicked or not
                if(data.clicked === true){
                    this.isButtonClicked = true;
                    this.buttonDiv.className = "btn border-0 p-md btn-light";
                    this.buttonDiv.disabled = false;
                    this.buttonDiv.innerHTML = `<span class="spinner-border text-light spinner-border-sm" role="status" 
                            aria-hidden="true" style="display: inline-block;"></span>
                            <span style="display: inline-block;">{{$buttonWording['clicked']}}</span>`;
                } else {
                    this.isButtonClicked = false;
                    this.buttonDiv.className = "btn border-0 p-md btn-primary";
                    this.buttonDiv.disabled = false;
                    this.buttonDiv.innerHTML = `<span>{{$buttonWording['normal']}}</span>`;
                }

            @elseif ($buttonType === 'cancel')
                let modalTarget = "";
                let modalToggle = "";
                
                if(this.callModal){
                    modalTarget = `data-coreui-target=#${this.callModal}`;
                    modalToggle = `data-coreui-toggle="modal"`;
                }

                if(data.clicked === true){
                    this.buttonDiv.outerHTML = `<a class="btn btn-border border-gray p-md me-2" 
                    ${modalTarget} ${modalToggle} id="${this.buttonDivId}">{{$buttonWording['clicked']}}</a>`;

                    this.buttonDiv = document.querySelector(`#${this.buttonDivId}`);
                } else {
                    this.buttonDiv.outerHTML = `<a class="btn btn-border border-gray p-md me-2" 
                    ${modalTarget} ${modalToggle} id="${this.buttonDivId}">{{$buttonWording['normal']}}</a>`;
                    
                    this.buttonDiv = document.querySelector(`#${this.buttonDivId}`);
                }

            @elseif ($buttonType === 'edit')
                //clicked or not
                if(data.clicked === true){
                    this.isButtonClicked = true;
                    this.buttonDiv.className = "btn btn-border border-gray px-3 p-sm me-3";
                    this.buttonDiv.disabled = true;
                    this.buttonDiv.innerHTML = `<svg class="icon-feather me-2">
                            <use xlink:href="{{asset('template/assets/icons/feather/feather-sprite.svg#edit-3')}}">
                            </use></svg> {{$buttonWording['clicked']}}`;
                } else {
                    this.isButtonClicked = false;
                    this.buttonDiv.className = "btn btn-border border-gray px-3 p-sm me-3";
                    this.buttonDiv.disabled = false;
                    this.buttonDiv.innerHTML = `<svg class="icon-feather me-2">
                        <use xlink:href="{{asset('template/assets/icons/feather/feather-sprite.svg#edit-3')}}">
                        </use></svg> {{$buttonWording['clicked']}}`;
                }
            @endif
        }
    }

    const {{$componentId}} = new {{$randomJavascriptClassName}}({
        'buttonDiv': "{{$id['buttonDiv']}}",
        'buttonWording': @json($buttonWording),
        'callModal': @json($callModal)
    });
</script>