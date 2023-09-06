@props([
    'autohide' => null,
    'data'
])

@if (!empty($data))
    <div>
        @if ($data['style'] === 'danger')
            <div id="alert" class="alert-profile-danger py-2 px-3">
        @elseif ($data['style'] === 'warning')
            <div id="alert" class="alert-profile-warning py-2 px-3">
        @endif
            <div class="d-flex justify-content-between">
                <div class="p-sm fw-medium">
                    @if (!empty($data['header']))
                        {{$data['header'].", "}}
                    @endif

                    @if (!empty($data['body']))
                        {{$data['body']}}
                    @endif
                </div>
                <svg id="hide-alert" class="icon-feather ms-2" style="cursor: pointer;min-width:20px;">
                    <use xlink:href="{{asset('template/assets/icons/feather/feather-sprite.svg#x')}}">
                    </use>
                </svg>
            </div>
        </div>
    </div>
@endif

<script>
    // note : plan of this is to create alert (refresh in some second using session), 
    //but change to become sticky information because this need to be persistent
    (function(){
        document.addEventListener("DOMContentLoaded", () => {
            const autoHide = @json($autohide ?? null);

            //for timer
            function triggerAlert(){
                let alertDiv = document.querySelector('#alert');

                function hideElement(){
                    alertDiv.style.display = 'none';
                }

                setTimeout(hideElement, autoHide);
            }

            function hideAlert(){
                let alertDiv = document.querySelector('#alert');
                let hideAlertDiv = document.querySelector('#hide-alert');
                if(hideAlertDiv){
                    hideAlertDiv.addEventListener('click', function(){
                        alertDiv.style.display = 'none';
                    })
                }
            }

            //init
            hideAlert();

            //if livewire updated then re-update the sticky
            Livewire.hook('message.processed', (message, component) => {
                let alertDiv = document.querySelector('#alert');
                let hideAlertDiv = document.querySelector('#hide-alert');

                if(alertDiv && autoHide != null){
                    triggerAlert();
                }

                if(hideAlertDiv){
                    hideAlert();
                }
            });
        });
    })();
    
</script>