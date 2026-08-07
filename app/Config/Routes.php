<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota padrão
$routes->get('/', 'Auth::login');

// Rotas de Autenticação
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');

// Rotas do Agendamento - GET
$routes->get('agenda', 'Agenda::index');
$routes->get('agenda/agendamentos', 'Agenda::agendamentos');
$routes->get('agenda/perfil', 'Agenda::perfil');

// ============================================
// ROTAS AJAX - IMPORTANTE: Registrar como POST
// ============================================
$routes->post('agenda/get_doctors', 'Agenda::getDoctors');
$routes->post('agenda/get_available_slots', 'Agenda::getAvailableSlots');
$routes->post('agenda/get_patient_appointments', 'Agenda::getPatientAppointments');
$routes->post('agenda/save_appointment', 'Agenda::saveAppointment');
$routes->post('agenda/cancelar_agendamento', 'Agenda::cancelarAgendamento');
$routes->post('agenda/atualizar-perfil', 'Agenda::atualizarPerfil');

// Rotas para agendamentos (detalhes e edição)
$routes->post('agenda/get_appointment_details', 'Agenda::getAppointmentDetails');
$routes->post('agenda/update_appointment', 'Agenda::updateAppointment');

// Rotas para perfil
$routes->post('agenda/atualizar-perfil', 'Agenda::atualizarPerfil');

// Logout
$routes->get('logout', 'Auth::logout');

// ============================================
// ROTAS DO ADMIN
// ============================================

// Páginas principais
$routes->get('admin', 'Admin::index');
$routes->get('admin/pacientes', 'Admin::pacientes');
$routes->get('admin/medicos', 'Admin::medicos');
$routes->get('admin/secretarios', 'Admin::secretarios');
$routes->get('admin/agendamentos', 'Admin::agendamentos');

// Cadastros
$routes->get('admin/cad_paciente', 'Admin::cadPaciente');
$routes->get('admin/cad_secretario', 'Admin::cadSecretario');
$routes->get('admin/cad_medico', 'Admin::cadMedico');
$routes->get('admin/cad_agendamento', 'Admin::cadAgendamento');
$routes->get('admin/disponibilidade', 'Admin::disponibilidade');

// Relatórios e configurações
$routes->get('admin/relatorios', 'Admin::relatorios');
$routes->get('admin/configuracoes', 'Admin::configuracoes');

// ============================================
// ROTAS AJAX DO ADMIN
// ============================================

// Métricas e atividades
$routes->get('admin/metrics', 'Admin::metrics');
$routes->get('admin/activity', 'Admin::activity');

// Buscar dados específicos
$routes->get('admin/patient/(:any)', 'Admin::patient/$1');
$routes->get('admin/doctor/(:any)', 'Admin::doctor/$1');

// Atualizações
$routes->post('admin/update_patient', 'Admin::updatePatient');
$routes->post('admin/update_doctor', 'Admin::updateDoctor');

// Operações com agendamentos
$routes->post('admin/cancel_appointment', 'Admin::cancelAppointment');
$routes->post('admin/delete_appointment', 'Admin::deleteAppointment');

// ============================================
// ROTAS PARA GRÁFICOS DO ADMIN
// ============================================
$routes->get('admin/chart_appointments_trend', 'Admin::chartAppointmentsTrend');
$routes->get('admin/chart_appointments_status', 'Admin::chartAppointmentsStatus');
$routes->get('admin/chart_doctors_specialty', 'Admin::chartDoctorsSpecialty');
$routes->get('admin/chart_patients_monthly', 'Admin::chartPatientsMonthly');

// ============================================
// ROTAS PARA PACIENTES
// ============================================
$routes->get('admin/get_patients', 'Admin::getPatients');
$routes->post('admin/get_patient_details', 'Admin::getPatientDetails');
$routes->post('admin/delete_patient', 'Admin::deletePatient');
$routes->post('admin/update_patient', 'Admin::updatePatient');

// ============================================
// ROTAS PARA MÉDICOS
// ============================================
$routes->get('admin/get_doctors', 'Admin::getDoctors');
$routes->post('admin/get_doctor_details', 'Admin::getDoctorDetails');
$routes->post('admin/delete_doctor', 'Admin::deleteDoctor');
$routes->post('admin/update_doctor', 'Admin::updateDoctor');

// ============================================
// ROTAS PARA SECRETÁRIOS
// ============================================
$routes->get('admin/get_secretaries', 'Admin::getSecretaries');
$routes->post('admin/get_secretary_details', 'Admin::getSecretaryDetails');
$routes->post('admin/delete_secretary', 'Admin::deleteSecretary');
$routes->post('admin/update_secretary', 'Admin::updateSecretary');

// ============================================
// ROTAS PARA AGENDAMENTOS
// ============================================
$routes->get('admin/get_appointments', 'Admin::getAppointments');
$routes->post('admin/get_appointment_details', 'Admin::getAppointmentDetails');
$routes->post('admin/delete_appointment', 'Admin::deleteAppointment');
$routes->post('admin/cancel_appointment', 'Admin::cancelAppointment');

// Rotas para médicos
$routes->get('admin/get_doctors', 'Admin::getDoctors');
$routes->post('admin/get_doctor_details', 'Admin::getDoctorDetails');
$routes->post('admin/delete_doctor', 'Admin::deleteDoctor');
$routes->post('admin/update_doctor', 'Admin::updateDoctor');

// Rotas para secretários
$routes->get('admin/get_secretaries', 'Admin::getSecretaries');
$routes->post('admin/get_secretary_details', 'Admin::getSecretaryDetails');
$routes->post('admin/delete_secretary', 'Admin::deleteSecretary');
$routes->post('admin/update_secretary', 'Admin::updateSecretary');

// Rotas para agendamentos
$routes->get('admin/get_appointments', 'Admin::getAppointments');
$routes->post('admin/get_appointment_details', 'Admin::getAppointmentDetails');
$routes->post('admin/delete_appointment', 'Admin::deleteAppointment');
$routes->post('admin/cancel_appointment', 'Admin::cancelAppointment');
$routes->post('admin/update_appointment', 'Admin::updateAppointment');

// Rotas para cadastro de pacientes
$routes->post('admin/create_patient', 'Admin::createPatient');

// Rotas para secretários
$routes->post('admin/create_secretary', 'Admin::createSecretary');
$routes->post('admin/update_secretary', 'Admin::updateSecretary');

// Rotas para médicos
$routes->post('admin/create_doctor', 'Admin::createDoctor');

// Rotas para relatórios
$routes->post('admin/get_report_data', 'Admin::getReportData');
$routes->post('admin/export_report', 'Admin::exportReport');

// Rotas para configurações
$routes->post('admin/save_hospital_config', 'Admin::saveHospitalConfig');
$routes->post('admin/save_system_config', 'Admin::saveSystemConfig');
$routes->post('admin/save_schedule_config', 'Admin::saveScheduleConfig');
$routes->post('admin/delete_user', 'Admin::deleteUser');
$routes->get('admin/add_user', 'Admin::addUser');
$routes->get('admin/edit_user', 'Admin::editUser');

// Rotas para disponibilidade
$routes->get('admin/disponibilidade', 'Admin::disponibilidade');
$routes->post('admin/save_schedule', 'Admin::saveSchedule');
$routes->post('admin/delete_schedule', 'Admin::deleteSchedule');

// Rotas para agendamentos
$routes->get('admin/cad_agendamento', 'Admin::cadAgendamento');
$routes->post('admin/create_appointment', 'Admin::createAppointment');
