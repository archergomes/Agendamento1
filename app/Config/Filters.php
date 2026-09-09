<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
    ];

    public $globals = [
        'before' => [
            'csrf' => [
                'except' => [
                    // Rotas de autenticação
                    'auth/login',
                    'auth/register',
                    
                    // Rotas do Admin - AJAX
                    'admin/get_doctors',
                    'admin/get_available_slots',
                    'admin/get_patient_appointments',
                    'admin/save_appointment',
                    'admin/cancel_appointment',
                    'admin/delete_appointment',
                    'admin/atualizar-perfil',
                    'admin/get_appointment_details',
                    'admin/update_appointment',
                    
                    // Rotas de pacientes
                    'admin/get_patients',
                    'admin/get_patient_details',
                    'admin/delete_patient',
                    'admin/update_patient',
                    'admin/create_patient',
                    
                    // Rotas de médicos
                    'admin/get_doctors',
                    'admin/get_doctor_details',
                    'admin/delete_doctor',
                    'admin/update_doctor',
                    'admin/create_doctor',
                    
                    // Rotas de secretários
                    'admin/get_secretaries',
                    'admin/get_secretary_details',
                    'admin/delete_secretary',
                    'admin/update_secretary',
                    'admin/create_secretary',
                    
                    // Rotas de agendamentos
                    'admin/get_appointments',
                    'admin/get_appointment_details',
                    'admin/delete_appointment',
                    'admin/cancel_appointment',
                    'admin/update_appointment',
                    
                    // Rotas de métricas e gráficos
                    'admin/metrics',
                    'admin/activity',
                    'admin/chart_appointments_trend',
                    'admin/chart_appointments_status',
                    'admin/chart_doctors_specialty',
                    'admin/chart_patients_monthly',

                    'agenda/cancelar_agendamento',
                    'agenda/save_appointment',

                    // Rotas de relatórios - ADICIONAR ESTAS
                    'admin/get_report_data',
                    'admin/export_report'
                ]
            ],
        ],
        'after' => [
            'toolbar',
        ],
    ];

    public $methods = [];

    public $filters = [];
}