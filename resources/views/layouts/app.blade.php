<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css'>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @include('layouts.nav')
    <div class="container">
        <form action="{{ route('update') }}" method="post">
            {{ csrf_field() }}
            <div class="col-sm-2">
                <legend>Enter The Token</legend>
            </div>
            <div class="col-sm-3"><input class="form-control" id="focusedInput" type="text" name="token" required></div>
            <div class="col-sm-2">
                <legend>Custom server</legend>
            </div>
            <div class="col-sm-3"><input class="form-control" type="text" name="Server" placeholder="blynk-cloud.com">
            </div>
            @if (!Auth::user()->token == null && !Auth::user()->server == null)
            <div class="col-sm-2"> <button class="btn btn-primary">Change</button><a href="/logout"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit()"
                    class="btn btn-danger">
                    <div class="text">Logout</div>
                </a>
            </div>
            @else
            <div class="col-sm-2"> <button class="btn btn-primary">Submit</button><a href="/logout"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit()"
                    class="btn btn-danger">
                    <div class="text">Logout</div>
                </a>
            </div>
            @endif
        </form>
        <form action="/logout" method="POST" id="logout-form">@csrf</form>
    </div>
    @if (!Auth::user()->token == null && !Auth::user()->server == null)
    <br>
    <div class="container">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Project Information</h3>
            </div>
            <div class="panel-body">
                <div class="container">
                    <div class="col-sm-3"><label>Project Name :</label></div>
                    <div class="col-sm-9"><label id="pn">{{ $Projectinfo['devices'][0]['name'] }} </label></div>
                    <div class="col-sm-3"><label>Board Type :</label></div>
                    <div class="col-sm-9"><label id="bt">{{ $Projectinfo['devices'][0]['boardType'] }}</label></div>
                    <div class="col-sm-3"><label>Connection Type :</label></div>
                    <div class="col-sm-9"><label id="bt">{{ $Projectinfo['devices'][0]['connectionType'] }}</label>
                    </div>
                    <div class="col-sm-3"><label>Hardware Status :</label></div>
                    @if ($isHardwareConnected == 'true')
                    <div class="col-sm-9"><label id="hs"><span class="label label-success">ONLINE</span></label></div>
                    @else
                    <div class="col-sm-9"><label id="hs"><span class='label label-danger'>OFFLINE</span></label></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="container ">
        <div class="container">
            <form action="{{ route('addButton') }}" method="post">
                {{ csrf_field() }}
                <div class="col-sm-2">
                    <legend>Name</legend>
                </div>
                <div class="col-sm-2">
                    <input class="form-control" type="text" name="name" placeholder="Bedroom" required></div>
                <div class="col-sm-2">
                    <legend>Pin</legend>
                </div>
                <div class="col-sm-2">
                    <input class="form-control" type="text" name="pin" placeholder="D0" required></div>
                <div class="col-sm-2">
                    <select class="form-control" name="type" required>
                        <option>Button</option>
                        <option>Slider</option>
                    </select>
                </div>
                <div class="col-sm-2"> <button class="btn btn-primary">Submit</button></div>
        </div>
        </form>
        <br>
        <div class="text-center" id="adddata">
            @foreach ($databuttons as $item)
            @if ($item->type == 'Button')
            <div id="div{{ $item->pin }}" class='alert'>
                <div class='col-sm-2'>
                    <legend><label>{{ $item->name }}</label><br>{{ $item->pin }}</legend>
                </div>
                @if($value[$item->id] == 'ON')
                <div class='col-sm-2'><label class='switch'><input type='checkbox' id="{{ $item->pin }}"
                            value='{{ $value[$item->id] }}' onclick='toggleCheckbox(this)' checked>
                        <div class='slider round'></div>
                    </label></div>
                @else
                <div class='col-sm-2'><label class='switch'><input type='checkbox' id="{{ $item->pin }}"
                            value='{{ $value[$item->id] }}' onclick='toggleCheckbox(this)'>
                        <div class='slider round'></div>
                    </label></div>
                @endif
                <div class='col-sm-2'><button onclick="RemoveButton({{ $item->id }})" class='btn btn-danger'>Remove</button>
                </div>
            </div><br><br>
            @elseif ($item->type == 'Slider')
            <div id="div{{ $item->pin }}" class='alert'>
                <div class='col-sm-2'>
                    <legend><label>{{ $item->name }}</label><br>{{ $item->pin }}</legend>
                </div>
                <div class='col-sm-2'><input id="{{ $item->pin }}" type='range' min='0' max='100'
                        value='{{ $value[$item->id] }}' onchange='ranchang(this)' /></div>
                <div class='col-sm-2'><a href='{{ route('deleteButton', $item->id) }}' class='btn btn-danger'>Remove</a>
                </div>
            </div><br><br>
            @endif
            @endforeach
        </div>
    </div>

    </div>
    <script>
        function ranchang(element) {
            var e = element.id;
            currentvalue = document.getElementById(e).value;
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'info',
                title: `Value is changed to ${currentvalue}`
            })
            $.ajax({
                url: "{{ url('api/update') }}",
                type: "get",
                dataType: "json",
                data: {
                    "_token": $('#token').val(),
                    "pin": e,
                    "value": currentvalue
                }
            });
        }

        function toggleCheckbox(element) {
            var e = element.id;
            currentvalue = document.getElementById(e).value;
            if (currentvalue == "ON") {
                var c = 0
                document.getElementById(e).value = "OFF";
            } else {
                var c = 1
                document.getElementById(e).value = "ON";
            }
            var t = `{{ Auth::user()->token }}`
            var cs = `{{ Auth::user()->server }}`
            $.ajax({
                url: "{{ url('api/update') }}",
                type: "get",
                dataType: "json",
                data: {
                    "_token": $('#token').val(),
                    "pin": e,
                    "value": c
                }
            });
        }

        function RemoveButton(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.href = "{{ url('deleteButton') }}/" + id;
                }
            })
        }

    </script>
    @endif
    @include('sweetalert::alert')
</body>

</html>
