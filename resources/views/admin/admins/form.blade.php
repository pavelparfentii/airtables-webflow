@csrf

@isset($admin->id)
    {{ method_field('PATCH')}}
@endisset

<div class="card-body">
    <div class="row">

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="exampleInputName">Ім'я</label>
                        <input name="name"
                               type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="exampleInputName"
                               placeholder="Введіть ім'я"
                               value="{{old('name', $admin->name)}}" />
                        <div>{{$errors ->first('name')}}</div>
                    </div>


{{--                    <div class="form-group col-md-12 mb-2">--}}
{{--                        <label for="avatar">Аватар</label>--}}
{{--                        <div id="fm" style="height: 600px;"></div>--}}
{{--                        <input type="hidden" name="avatar" id="avatar">--}}
{{--                    </div>--}}
                    <div class="form-group col-md-6">
                        <label for="avatar">Аватар</label>
                        <div class="input-group">
                            <input id="avatar"
                                   class="form-control"
                                   type="text"
                                   name="avatar"
                                   value="{{old('avatar', $admin->avatar)}}"
                            >
                            <span class="input-group-btn">
                                <button id="lfm" data-input="avatar" data-preview="holder" class="btn btn-primary">
                                    <i class="fa fa-picture-o"></i> Обрати
                                </button>
                            </span>
                        </div>
                        <img id="holder" style="margin-top:15px;max-height:100px;">
                    </div>
                    <img id="holder" style="margin-top:15px;max-height:100px;">

                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail">E-mail</label>
                        <input name="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               placeholder="E-mail" value="{{old('email', $admin->email)}}">
                        <div>{{$errors ->first('email')}}</div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="exampleInputPassword">Пароль</label>
                        <input name="password"
                               type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="exampleInputPassword"
                               placeholder="Пароль">
                        <div>{{$errors ->first('password')}}</div>
                    </div>


                    <div class="form-group col-4">
                        <label for="exampleInputStatus">Статус</label>
                        <select name="status" id="status" class="form-control col-md-6">
                            <option value="" disabled>Оберіть статус</option>
                            <option value="1" {{ $admin->is_active === 1 ? 'selected' : '' }}>Активний</option>
                            <option value="0" {{ $admin->is_active === 0 ? 'selected' : '' }}>Неактивний</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

