<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación
    |--------------------------------------------------------------------------
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other sea :value.',
    'active_url' => 'El campo :attribute debe ser una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo debe contener letras.',
    'alpha_dash' => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo debe contener letras y números.',

    'array' => 'El campo :attribute debe ser un arreglo.',

    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',

    'between' => [
        'array' => 'El campo :attribute debe contener entre :min y :max elementos.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],

    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',

    'date' => 'El campo :attribute debe ser una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute debe coincidir con el formato :format.',

    'decimal' => 'El campo :attribute debe tener :decimal decimales.',

    'different' => 'Los campos :attribute y :other deben ser diferentes.',

    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',

    'email' => 'El campo :attribute debe ser una dirección de correo válida.',

    'exists' => 'El valor seleccionado para :attribute no es válido.',

    'file' => 'El campo :attribute debe ser un archivo.',

    'image' => 'El campo :attribute debe ser una imagen.',

    'in' => 'El valor seleccionado para :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',

    'max' => [
        'array' => 'El campo :attribute no debe contener más de :max elementos.',
        'file' => 'El archivo :attribute no debe superar los :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],

    'mimes' => 'El archivo :attribute debe ser de tipo: :values.',
    'mimetypes' => 'El archivo :attribute debe ser de tipo: :values.',

    'min' => [
        'array' => 'El campo :attribute debe contener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],

    'not_in' => 'El valor seleccionado para :attribute no es válido.',

    'numeric' => 'El campo :attribute debe ser un número.',

    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other sea :value.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other sea :value.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values esté presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values estén presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no esté presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values esté presente.',

    'same' => 'Los campos :attribute y :other deben coincidir.',

    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El archivo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],

    'string' => 'El campo :attribute debe ser texto.',

    'unique' => 'El valor de :attribute ya está registrado.',

    /*
    |--------------------------------------------------------------------------
    | Mayor o igual que
    |--------------------------------------------------------------------------
    */

    'gte' => [
        'array' => 'El campo :attribute debe contener :value elementos o más.',
        'file' => 'El archivo :attribute debe ser mayor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o más.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mayor que
    |--------------------------------------------------------------------------
    */

    'gt' => [
        'array' => 'El campo :attribute debe contener más de :value elementos.',
        'file' => 'El archivo :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Menor o igual que
    |--------------------------------------------------------------------------
    */

    'lte' => [
        'array' => 'El campo :attribute no debe contener más de :value elementos.',
        'file' => 'El archivo :attribute debe ser menor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute no debe tener más de :value caracteres.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Menor que
    |--------------------------------------------------------------------------
    */

    'lt' => [
        'array' => 'El campo :attribute debe contener menos de :value elementos.',
        'file' => 'El archivo :attribute debe ser menor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados
    |--------------------------------------------------------------------------
    */

    'custom' => [

        'capacidad_maxima' => [
            'gte' => 'La capacidad máxima debe ser mayor o igual que la capacidad mínima.',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres amigables de atributos
    |--------------------------------------------------------------------------
    */

    'attributes' => [

        'rut_persona' => 'RUT de la persona',
        'rut_entidad' => 'RUT de la entidad',
        'rut_encargado' => 'RUT del encargado',

        'categoria_servicio_id' => 'categoría',
        'codigo' => 'código',
        'nombre' => 'nombre',
        'descripcion' => 'descripción',
        'imagen' => 'imagen',
        'duracion_minutos' => 'duración',
        'capacidad_minima' => 'capacidad mínima',
        'capacidad_maxima' => 'capacidad máxima',
        'precio' => 'precio',
        'tipo_cobro' => 'tipo de cobro',
        'requiere_reserva' => 'requiere reserva',

    ],

];