    <div class="row align-items-center mb-2">
        <div class="col-lg-12 col-xl-12">
            <div class="row g-1 align-items-center">
                @foreach ($columns as $column)
                    <div class="col">
                        <div class="card shadow-lg h-100">
                            <div class="card-body rounded p-2 text-truncate d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title mb-2 text-center text-truncate">{{ $column->title }}</h6>
                                <div id="{{ $column->alias }}" class="d-flex justify-content-center align-items-center">
                                    <a href="javascript:void(0)" onclick="{{ $loadTotatableFunction }}" class="btn btn-link p-0">
                                        {{ __('snawbar-datatable::datatable.load') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>