@props([
    'componentId',
    'validationVariableName',
    'wireIgnore' => true
])

{{-- html id init with unique identifier --}}
@php
    $uniqueIdentifier = mt_rand();
    $id = [
        'errorDiv' => 'errorDiv',
    ];

    $randomJavascriptClassName = $componentId.$uniqueIdentifier;

    //append unique identifier
    foreach ($id as $key => $value) {
        $id[$key] = $value . '-' . $uniqueIdentifier;
    }
@endphp

<div {{ $wireIgnore ? 'wire:ignore' : '' }}>
    <span class='error-color p-sm' role="alert" id="{{$id['errorDiv']}}">
    </span>
</div>

<script>
    class {{$randomJavascriptClassName}}{
        constructor(){
            this.errorDiv = document.querySelector("#{{$id['errorDiv']}}");
            this.svgAlert = `<svg class="icon-danger">
                    <use href="{{asset('template/assets/icons/feather/feather-sprite.svg#alert-triangle')}}"></use>
                    </svg>`

            //error listener using livewire
            document.addEventListener("DOMContentLoaded", () => {
                Livewire.hook('message.processed', (message, component) => {
                    if(message.response.serverMemo.errors['{{$validationVariableName}}']){
                        this.setError({'errorMessage': message.response.serverMemo.errors['{{$validationVariableName}}'][0]});
                    }
                });
            });
        }

        setError(data){
            this.errorDiv.innerHTML = this.svgAlert;
            this.errorDiv.innerHTML += data.errorMessage;
        }

        resetError(){
            this.errorDiv.innerHTML = "";
        }
    }

    const {{$componentId}} = new {{$randomJavascriptClassName}}();
</script>
