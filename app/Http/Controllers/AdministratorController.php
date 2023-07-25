<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\career;
use App\Models\course;
use App\Models\Pensum;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;

use App\Models\UserType;

use App\Models\Departament;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Models\AcademicLapse;
use App\Models\AcademicOffer;
use App\Models\DetailSection;
use App\Exports\MentionsExport;
use App\Exports\StudentsExport;
use App\Exports\SubjectsExport;
use App\Exports\TeachersExport;
use App\Models\EnrolledSubject;
use App\Exports\UserTypesExport;
use App\Exports\DepartmentsExport;
use App\Models\AcademicCurriculum;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Database\Seeders\UserTypesSeeder;
use App\Exports\StudentHistoriesExport;
use Illuminate\Support\Facades\Storage;
use App\Exports\StructureSectionsExport;
use App\Exports\AcademicCurriculumExport;
use App\Exports\DepartmentSectionsExport;
use App\Exports\DepartmentResourcesExport;

class AdministratorController extends Controller
{
    public function migrate(){
        return view('administrator.database.migrate');
    }

    public function load_data(){
        $students = DB::connection('mysql2')->table('dace002')->get();
        $teachers = DB::connection('mysql2')->table('tblaca007')->get();
        $enrolled = DB::connection('mysql2')->table('dace006')->get();//Materias inscritas
        $pensum = DB::connection('mysql2')->table('tblaca009')->get();//Pensum
        $academic_offers = DB::connection('mysql2')->table('tblaca004')->get();//Oferta académica
        $courses =DB::connection('mysql2')->table('tblaca008')->get(); //Asignatura
        $careers = DB::connection('mysql2')->table('tblaca010')->get(); //Carrera

        //Recorremos estudiante para poder conseguir los valores de ellos

        foreach($students as $student){
            //Datos usuarios
            $dni = $student->CI_E;
            $names = $student->NOMBRES;
            $last_names = $student->APELLIDOS;
            $gender = ($student->SEXO == 1) ? 'Masculino' : 'Femenino';
            $address = $student->DIRP_E;
            $telephone = $student->TELFP_E;
            $marital_status = $student->EDO_C_E;
            $country = $student->P_NAC_E;
            $state = $student->EDO_NAC_E;
            $town = $student->L_NAC_E;
            $birth_date = $student->F_NAC_E;
            $nationality = $student->NAC_E;
            $password = bcrypt($dni);
            //Datos estudiante
            $record = $student->EXP_E;
            $enrollment_lapse = $student->LAPSO_IN;
            $student_career = $student->C_UNI_CA;
            $c_entry = $student->C_INGRESO;
            $u_approved_credit = $student->U_CRED_APROB;
            $enrolled_credit_units = $student->U_CRED_INSC;
            $u_c_p_index = $student->U_C_P_INDIC;
            $academic_index = $student->IND_ACAD;
            $performance_index = $student->IND_REND;
            $conduct = $student->CONDUCTA;
            $semester = $student->SEMESTRE;
            $student_pensum = $student->PENSUM;
            $grade_date = $student->FECHA_GRADO;
            $lapse_index = $student->IND_LAPSO;
            $specialty_mention = $student->MENCION_ESP;
            $c_institute = $student->C_INSTITUTO;
            $u_cred_course_lapse = $student->U_CRED_CUR_LAPSO;
            $last_load = $student->ULTIMA_CARGA;
            $promotion_number = $student->NUM_PROMOCION;
            $grade_type = $student->TIPO_GRADO;

            //Creando el usuario estudiante
            User::create([
                'user_type' => 3,
                'dni' => $dni,
                'password'=> $password,
                'names' => $names,
                'last_names' => $last_names,
                'gender' => $gender,
                'address' => $address,
                'marital_status' => $marital_status,
                'country' => $country,
                'state' => $state,
                'town' => $town,
                'birth_date' => $birth_date,
                'telephone' => $telephone,
                'nationality' => $nationality,
                'status' => 'A'
            ]);

            //obtenemos el id del usuario creado
            $id_user = DB::table('users')->select('id')->orderByDesc('id')->first();

            //Registramos estudiante
            Student::create([
                'user' => $id_user->id,
                'proceedings' => $record,
                'enrollment_lapse' => $enrollment_lapse,
                'career' => $student_career,
                'c_entry' =>$c_entry,
                'u_approved_credit' => $u_approved_credit,
                'enrolled_credit_units' => $enrolled_credit_units,
                'u_c_p_index' => $u_c_p_index,
                'academic_index' => $academic_index,
                'performance_index' => $performance_index,
                'conduct' => $conduct,
                'semester' => $semester,
                'pensum' => $student_pensum,
                'grade_date' => $grade_date,
                'lapse_index' => $lapse_index,
                'specialty_mention' => $specialty_mention,
                'c_institute' => $c_institute,
                'u_cred_course_lapse' => $u_cred_course_lapse,
                'last_load' => $last_load,
                'promotion_number' => $promotion_number,
                'grade_type' => $grade_type
            ]);
        }

        //Recorremos profesor
        foreach($teachers as $teacher){
            //Datos usuario
            $dni = $teacher->CI;
            $names = $teacher->NOMBRE;
            $last_names = $teacher->APELLIDO;
            $gender = ($teacher->MASCULINO == 1 ? 'Masculino' : 'Femenino');
            $address = $teacher->DIRECCION;
            $telephone = $teacher->TELEFONO;
            $marital_status = $teacher->EDO_CIVIL;
            $country = $teacher->PAIS_ORIGEN;
            $birth_date = $teacher->FE_NACIM;
            $nationality = $teacher->NACIONALIDAD;
            $password = bcrypt($dni);

            //Datos profesor
            $undergraduate_title = $teacher->PRE_TITULO_OBT;
            $undergraduate_area = $teacher->PRE_AREA;
            $undergraduate_inst = $teacher->PRE_INSTITU;
            $undergraduate_country = $teacher->PRE_PAIS;
            $type_postgraduate = $teacher->POS_TIPO;
            $type_area = $teacher->POS_AREA;
            $postgraduate_inst = $teacher->POS_INSTITU;
            $postgraduate_country =$teacher->POS_PAIS;
            $postgraduate_completed = $teacher->POS_TERMINADO;
            $postgraduate_studies = $teacher->POS_ESTUDIO;
            $postgraduate_thesis_missing = $teacher->POS_FALTA_TESIS;
            $date_entry_public_adm = $teacher->FE_ING_ADM_PUB;
            $date_entry_unexpo = $teacher->FE_ING_UNEXPO;
            $sabbatical_date = $teacher->FE_LIC_SABAT;
            $retirement_date = $teacher->FE_JUBILACION;
            $dedication = $teacher->DEDICACION;
            $category = $teacher->CATEGORIA;
            $condition = $teacher->CONDICION;
            $section_code = $teacher->C_SECC_F;
            $assign_condition =$teacher->ASIGNA_CONC;
            $assign_dict =$teacher->ASIGNA_DICT;

            //Registramos usuario profesor
            User::create([
                'user_type' => 2,
                'dni' => $dni,
                'password'=> $password,
                'names' => $names,
                'last_names' => $last_names,
                'gender' => $gender,
                'address' => $address,
                'marital_status' => $marital_status,
                'country' => $country,
                'birth_date' => $birth_date,
                'telephone' => $telephone,
                'nationality' => $nationality,
                'status' => 'A'
            ]);

            //Obtenemos el id del usuario profesor
            $id_user = DB::table('users')->select('id')->orderByDesc('id')->first();

            //Registramos profesor
            Teacher::create([
                'user' => $id_user->id,
                'undergraduate_title' =>$undergraduate_title,
                'undergraduate_area' => $undergraduate_area,
                'undergraduate_inst' => $undergraduate_inst,
                'undergraduate_country' => $undergraduate_country,
                'type_postgraduate' => $type_postgraduate,
                'type_area' => $type_area,
                'postgraduate_inst' => $postgraduate_inst,
                'postgraduate_country' => $postgraduate_country,
                'postgraduate_completed' => $postgraduate_completed,
                'postgraduate_studies' => $postgraduate_studies,
                'postgraduate_thesis_missing' => $postgraduate_thesis_missing,
                'date_entry_public_adm' => $date_entry_public_adm,
                'date_entry_unexpo' => $date_entry_unexpo,
                'sabbatical_date' => $sabbatical_date,
                'retirement_date' => $retirement_date,
                'dedication' => $dedication,
                'category' => $category,
                'condition' => $condition,
                'section_code' => $section_code,
                'assign_condition'=> $assign_condition,
                'assign_dict' => $assign_dict
            ]);
        }

        //Recorremos materias inscritas
        foreach($enrolled as $enroll){
            $record = $enroll->ACTA;
            $lapse = $enroll->LAPSO;
            $course= $enroll->C_ASIGNA;
            $student_record = $enroll->EXP_E;
            $qualification = $enroll->CALIFICACION;
            $aff_index = $enroll->AFEC_INDICE;
            $status = $enroll->STATUS;
            $section = $enroll->SECCION;
            $c_status_note = $enroll->STATUS_C_NOTA;
            $date = $enroll->FECHA;
            $includes = $enroll->INCLUYE;

            //Registramos materias inscritas

            EnrolledSubject::create([
                'record' => $record,
                'lapse' => $lapse,
                'course' => $course,
                'student_record' => $student_record,
                'qualification' => $qualification,
                'aff_index' => $aff_index,
                'status' => $status,
                'section' => $section,
                'c_status_note' => $c_status_note,
                'date' => $date,
                'includes' => $includes
            ]);
        }

        //Recorremos pensum
        foreach($pensum as $pensu){
            $course = $pensu->C_ASIGNA;
            $career = $pensu->C_UNI_CA;
            $semester = $pensu->SEMESTRE;
            $credit_units = $pensu->U_CREDITOS;
            $subject_precondition1 = $pensu->PRE_COD_ASIG1;
            $subject_precondition2 = $pensu->PRE_COD_ASIG2;
            $subject_precondition3 = $pensu->PRE_COD_ASIG3;
            $subject_precondition4 = $pensu->PRE_COD_ASIG4;
            $subject_precondition5 = $pensu->PRE_COD_ASIG5;
            $cred_units_required = $pensu->UNI_CRED_REQ;
            $corequisite_code1 = $pensu->PAR_COD_ASIG1;
            $corequisite_code2 = $pensu->PAR_COD_ASIG2;
            $corequisite_code3 = $pensu->PAR_COD_ASIG3;
            $obligatory = $pensu->OBLIGATORIA;
            $elective = $pensu->ELECTIVA;
            $chain_rep = $pensu->REP_CADENA;
            $lapse = $pensu->LAPSO;
            $pensum_unexpo = $pensu->PENSUM;
            $mention = $pensu->MENCION;

            //Registramos pensum
            Pensum::create([
                'course'=> $course,
                'career' => $career,
                'semester' => $semester,
                'credit_units' => $credit_units,
                'subject_precondition1' => $subject_precondition1,
                'subject_precondition2' => $subject_precondition2,
                'subject_precondition3' => $subject_precondition3,
                'subject_precondition4' => $subject_precondition4,
                'subject_precondition5' => $subject_precondition5,
                'cred_units_required' => $cred_units_required,
                'corequisite_code1' => $corequisite_code1,
                'corequisite_code2' => $corequisite_code2,
                'corequisite_code3' => $corequisite_code3,
                'obligatory' => $obligatory,
                'elective' => $elective,
                'chain_rep' => $chain_rep,
                'lapse' => $lapse,
                'pensum' => $pensum_unexpo,
                'mention' =>$mention
            ]);
        }

        //Recorremos la oferta académica
        foreach($academic_offers as $academic_offer){
            $course = $academic_offer->C_ASIGNA;
            $lapse = $academic_offer->LAPSO;
            $sections = $academic_offer->SECCION;
            $inscribed = $academic_offer->INSCRITOS;
            $teacher_dni = $academic_offer->CI;
            $record = $academic_offer->ACTA;
            $top_cup = $academic_offer->TOT_CUP;

            //Registramos la oferta académica

            AcademicOffer::create([
                'course' => $course,
                'lapse' => $lapse,
                'sections' =>  $sections,
                'inscribed' => $inscribed,
                'teacher_dni' => $teacher_dni,
                'record' => $record,
                'top_cup' => $top_cup
            ]);
        }

        //Recorremos las asignaturas
        foreach($courses as $cours){
            $code = $cours->C_ASIGNA;
            $name = $cours->ASIGNATURA;
            $credit_units = $cours->UNID_CREDITO;
            $theoretical_hours = $cours->HORAS_TEORICAS;
            $practical_hours = $cours->HORAS_PRACTICAS;
            $laboratory_hours = $cours->HORAS_LAB;
            $section_code = $cours->C_SECC_F;

            //Registramos las asignaturas
            course::create([
                'code' => $code,
                'name' => $name,
                'credit_units' => $credit_units,
                'theoretical_hours' => $theoretical_hours,
                'practical_hours' => $practical_hours,
                'laboratory_hours' => $laboratory_hours,
                'section_code' => $section_code
            ]);
        }

        //Recorremos las carreras
        foreach($careers as $care){
            $code = $care->C_UNI_CA;
            $university = $care->UNIVERSIDAD;
            $career = $care->CARRERA;

            //Registramos las carreras
            career::create([
                'code' => $code,
                'university' => $university,
                'career' => $career
            ]);
        }

        return redirect()->action([AdministratorController::class, 'migrate'])->with('migrate-message','Migración finalizada exitosamente');
    }

    public function export(){
        return view('administrator.database.export');
    }

    public function exportStore(Request $request){
        $message = [
            'option.required' =>'Debe seleccionar una opción'
        ];
        $this->validate($request,[
            'option' => 'required'
        ],$message);
        if($request['option']== 1){
            return Excel::download(new UsersExport, 'users.xlsx');
        }
        if($request['option']== 2){
            return Excel::download(new UserTypesExport, 'user_types.xlsx');
        }
        if($request['option']== 3){
            return Excel::download(new TeachersExport, 'teachers.xlsx');
        }
        if($request['option']== 4){
            return Excel::download(new StudentsExport, 'students.xlsx');
        }
        if($request['option']== 5){
            return Excel::download(new DepartmentsExport, 'department.xlsx');
        }
        if($request['option']== 6){
            return Excel::download(new DepartmentSectionsExport, 'department_sections.xlsx');
        }
        if($request['option']== 7){
            return Excel::download(new AcademicCurriculumExport, 'academic_curricula.xlsx');
        }
        if($request['option']== 8){
            return Excel::download(new SubjectsExport, 'subjects.xlsx');
        }
        if($request['option']== 9){
            return Excel::download(new MentionsExport, 'mentions.xlsx');
        }
        if($request['option']== 10){
            return Excel::download(new StructureSectionsExport, 'structure_sections.xlsx');
        }
        if($request['option']== 11){
            return Excel::download(new StudentHistoriesExport, 'student_histories.xlsx');
        }
        if($request['option']== 12){
            return Excel::download(new DepartmentResourcesExport, 'department_resources.xlsx');
        }

    }

    public function empty(){
        return view('administrator.database.empty');
    }

    public function emptyStore(){
        $academicLapse = AcademicLapse::join('sections','academic_lapses.id','=','sections.academic_lapseid')
            ->join('detail_sections','sections.id','=','detail_sections.sectionid')
            ->where('sections.status','=','F')
            ->where('academic_lapses.status','=','F')
            ->select('academic_lapses.*')
            ->first();
       if($academicLapse){
        DetailSection::where('status','=','F')->delete();
        DB::table('sections')->where('status','=','F')->update([
            'teacherid' => null,
            'teacherid' =>null
        ]);
        return redirect()->action([AdministratorController::class, 'empty'])->with('mens','Se ha vaciado correctamente');
       }else{
        return redirect()->action([AdministratorController::class, 'empty'])->with('mens-error','No se puede ejecutar esta opción ya que hay un lapso académico activo o no hay datos que vaciar.');
       }

    }

    public function profile(User $user)
    {
        $id = auth()->user()->id;
        $admin = DB::table('users')
            ->join('user_types','users.user_type','=','user_types.id')
            /* ->where('users.user_type','=','1') */
            ->where('users.id','=',$id)
            ->select('users.*')
            ->first();
        //var_dump( $profile); die();
        return view('administrator.admin_profile', compact('admin','id'));

    }

    public function profile_update(Request $request, User $user)
    {

        $message = [
            'names.required' =>'El campo nombre es requerido',
            'names.max' => 'El máximo de caracteres es de 255',
            'last_names.required' =>'El campo apellido es requerido',
            'last_names.max' => 'El máximo de caracteres es de 255',
            'email.email' =>'Debe introducir un correo con formato valido.',
            'email.unique' => 'Ya existe un usuario con ese correo registrado',
            'photo.mimes' => 'Debe subir la foto con un formato valido como .jpg, .jpeg y .png',
            'phone1.regex' =>'El formato del campo es invalido',
            'phone1.max' =>'El máximo de caracteres es de 12.'

        ];
        // $this->validate($request,[
        //     'names' => 'required|max:255',
        //     // 'names' => 'max:255',
        //     'last_names' => 'required|max:255',
        //     // 'email' => 'email',
        //     // 'email' => 'unique:users'.$user->id,
        //     'phone1' => 'regex:/^[0-9]{4}-?[0-9]{7}/|max:12',
        // ],$message);

        $id = auth()->user()->id;
        // var_dump($request['phone1']); die();
        $data = $request->validate([

            'names' => ['required','string','max:255'],
            'last_names' => ['required','string','max:255'],
            'email' => ['email','nullable'],
            'photo' => ['mimes:jpg,jpeg,png'],
            'phone1' => ['regex:/^[0-9]{4}-?[0-9]{7}/','max:12','nullable'],


        ],$message);
        //$photo = $request->file('photo')->store('public/profile');

        if(isset($data['photo'])){
            $photo = $request->file('photo')->store('public/profile');
            $img_url = DB::table('users')
                ->where('id','=', $id)
                ->select('photo')
                ->first();
            if($img_url){
                $file = str_replace('storage', 'public', $img_url->photo);
                Storage::delete($file);
            }
            $photo_url = Storage::url($photo);
            $user->photo = $photo_url;
        }

        $user->names = $data['names'];
        $user->last_names = $data['last_names'];
        $user->email = $data['email'];
        $user->telephone =  $data['phone1'];


        $user->save();
        return redirect()->action([AdministratorController::class, 'profile'])->with('store','Usuario actualizado exitosamente');

        //var_dump($data); die();

    }


    public function users_create(Request $request){

        $user_types = UserType::where('user_types.status','=', 'A')->orderBy('user_types.id')->get();

        $departaments = DB::table('departaments')
        ->where('departaments.status','=', 'A')
        ->select('departaments.id', 'departaments.name')
        ->orderBy('departaments.id');

        $departaments = $departaments->get();

        $mentions = DB::table('mentions')
        ->where('mentions.status','=', 'A')
        ->select('mentions.id', 'mentions.name')
        ->orderBy('mentions.id');

        $mentions = $mentions->get();

        return view('administrator.users.create',['user_types'=>$user_types, 'departaments'=>$departaments,'mentions'=>$mentions ]);

    }

    public function users_create_store(Request $request){
        $id = auth()->user()->id;

        //var_dump( $profile); die();

        $data = $request->validate([
            'dni' => ['required', 'numeric','digits_between:6,9', 'unique:users'],
            'names' => ['required','regex:/^[\pL\s\-]+$/u', 'max:255'],
            'last_names' => ['required','regex:/^[\pL\s\-]+$/u', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['regex:/^[0-9]{4}-?[0-9]{7}/','max:12'],
            'password' => ['required','string','min:8'],
            'user_types' => ['required','string'],

            'departmentid' => ['string'],
            'mentionid' => ['string'],
            'college_degree' => ['string'],
        ]);

        $password = bcrypt($data['dni']);

        return view('administrator.users.create');

        $usuario = User::create([
            'dni'=>$data['dni'],
            'names' => $data['names'],
            'last_names' => $data['last_names'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'password' => $password,
            'user_types'=> $data['user_types'],
            'status' => 'A',
        ]);

        Teacher::create([
            'departmentid' => $data['departaments'],
            'mentionid' => $data['mentions'],
            'college_degree' => $data['college_degree'],
            'userid' => $usuario->id,
            'status' => 'A',
        ]);

        return redirect()->action([AdministratorController::class, 'users_create'])->with('user-message','Usuario registrado con exito');
    }

    public function users_restore(){
        return view('administrator.users.restore');
    }

    public function users_restore_password(Request $request){
        if($request['dni']==""){
            return redirect()->action([AdministratorController::class, 'users_restore'])->with('user-message-error','Campo cédula requerido');
        }
        $dni = DB::table('users')->where('dni', '=', $request['dni'])->first();
        if(!$dni){
            return redirect()->action([AdministratorController::class, 'users_restore'])->with('user-message-error','Cédula no registrada');
        }
        $password = bcrypt($request['dni']);
        DB::table('users')
            ->where('dni','=', $request['dni'])
            ->update(['password'=> $password]);
            return redirect()->action([AdministratorController::class, 'users_restore'])->with('user-message-restore','Contraseña restablecida');
    }
    public function users_restore_factor(Request $request){
        if($request['user_factor']==""){
            return redirect()->action([AdministratorController::class, 'users_restore'])->with('user-message-error','Campo cédula requerido');
        }
        $dni = DB::table('users')->where('dni', '=', $request['user_factor'])->first();
        if(!$dni){
            return redirect()->action([AdministratorController::class, 'users_restore'])->with('user-message-error','Cédula no registrada');
        }
        DB::table('users')
            ->where('dni','=', $request['user_factor'])
            ->update(['two_factor_secret'=>null,'two_factor_recovery_codes'=>null]);
            return redirect()->action([AdministratorController::class, 'users_restore'])->with('user-message-restore','Autenticación de dos factores desactivado');
    }

    public function users_modify(){
        $users = DB::table('users')
            ->join('user_types', 'user_types.id','=','users.user_type')
            ->where('users.status','=', 'A')
            ->select('users.names','users.last_names', 'users.dni', 'user_types.description')
            ->orderBy('user_types.description')
            ->simplePaginate(6);
            return view('administrator.users.modify')->with('users',$users);
    }
}
