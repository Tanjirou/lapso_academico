<div>
    <div class="main-content mt-6">
        <div class="layout-px-spacing">
            <div class="mb-0">
                <div class="row mb-2 mt-3">
                    <div class="col-12 col-md-7 col-lg-7 col-xl-7 mb-0 order-md-0 order-1 mb-2 mt-2">
                        <nav class="breadcrumb-two align-self-center" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active"><a href="{{ route('home') }}">Inicio</a></li>
                                <li class="breadcrumb-item" ><a href="javascript:void(0);">Crear Estructura</a></li>
                                <!--<li class="breadcrumb-item" aria-current="page"><a href="javascript:void(0);">Secciones</a></li>-->
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-4 col-lg-4 col-xl-4 justify-content-end mb-0 d-flex mt-4 ml-auto">
                        <img src="assets/img/logo-SPA1.png" class="img-fluid mb-0 d-none d-md-block" alt="header-image" style="width-sm: 23%; width-md: 35%; width: 40%">
                    </div>
                </div>
            </div>
            <div class="row layout-spacing mt-2">
                <div class="col-lg-12">
                    <div class="statbox widget box box-shadow shadow ">
                        <div class="row mt-0 justify-content-center">
                            <div class="col-md-10 align-content-center">
                                <div class="row justify-content-center mb-3 mt-2 mr-auto ml-2 m-0">
                                    <h2 class="text-bold text-primary fond-bold m-0">CREAR ESTRUCTURA</h2>
                                </div>
                                <div class="row mt-0 justify-content-center">
                                    <div class=" col-11 col-md-10 pb-3">
                                        <form method="POST" wire:submit.prevent="store">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="textDpto">Departamento</label>
                                                            <input type="text" class="form-control" id="dpto" placeholder="Dpto" value="Estudios Básicos" readonly >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="textMenc">Sección del Departamneto</label>
                                                        <select class="form-control" id="textMenc">
                                                            <option>Seleccione</option>
                                                            <option>Matematica</option>
                                                            <option>Física</option>
                                                            <option>Asignaturas Generales</option>
                                                            <option>Ciencias Gráficas</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="textAsig">Asignatura</label>
                                                        <select class="form-control" id="textAsig">
                                                            <option>Seleccione</option>
                                                            <option>Calculo I</option>
                                                            <option>Algebra</option>
                                                            <option>Programación I</option>
                                                            <option>Fisica</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="textPunt">Cantidad de Secciones</label>
                                                        <input class="form-control" type="numeric" placeholder="4" >
                                                        <small id="sh-text1" class="form-text text-muted">Número aproximado de secciones a aperturar.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="textPunt">Cantidad de Estudiantes</label>
                                                        <input class="form-control" type="integer" placeholder="40" >
                                                        <small id="sh-text1" class="form-text text-muted">Cantidad máxima de estudiantes en promedio para la planificación del próximo semestre.</small>
                                                    </div>
                                                </div>
                                            </div>
                                                <br>
                                                <div class="row">
                                                    <div class="form-group text-center col-md-12">
                                                            <button class="btn btn-primary mr-2 btn-lg"> <a href="#"></a> Guardar </button>
                                                    </div>
                                                </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session()->has('mens'))
        <div class="alert alert-success">
            {{ session('mens') }}
        </div>
    @endif
</div>
