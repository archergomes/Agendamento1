<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota padrão
$routes->get('/', 'Auth::login');

// ============================================
// ROTAS DE AUTENTICAÇÃO
// ============================================
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');

// ============================================
// ROTAS DE RECUPERAÇÃO DE SENHA (NOVAS)
// ============================================
$routes->get('auth/recuperar-senha', 'Auth::recuperar_senha');
$routes->post('auth/recuperar-senha', 'Auth::recuperar_senha');
$routes->post('auth/enviar-link-recuperacao', 'Auth::enviar_link_recuperacao');
$routes->get('auth/redefinir-senha/(:any)', 'Auth::redefinir_senha/$1');
$routes->post('auth/redefinir-senha/(:any)', 'Auth::redefinir_senha/$1');
$routes->post('auth/atualizar-senha', 'Auth::atualizar_senha');

// ============================================
// ROTAS DO AGENDAMENTO
// ============================================
$routes->get('agenda', 'Agenda::index');
$routes->get('agenda/agendamentos', 'Agenda::agendamentos');
$routes->get('agenda/perfil', 'Agenda::perfil');

// ============================================
// ROTAS AJAX - AGENDAMENTO
// ============================================
$routes->post('agenda/get_doctors', 'Agenda::getDoctors');
$routes->post('agenda/get_available_slots', 'Agenda::getAvailableSlots');
$routes->post('agenda/get_patient_appointments', 'Agenda::getPatientAppointments');
$routes->post('agenda/save_appointment', 'Agenda::saveAppointment');
$routes->post('agenda/cancelar_agendamento', 'Agenda::cancelarAgendamento');
$routes->post('agenda/atualizar-perfil', 'Agenda::atualizarPerfil');
$routes->post('agenda/get_appointment_details', 'Agenda::getAppointmentDetails');
$routes->post('agenda/update_appointment', 'Agenda::updateAppointment');

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
// ROTAS PARA PACIENTES (ADMIN)
// ============================================
$routes->get('admin/get_patients', 'Admin::getPatients');
$routes->post('admin/get_patient_details', 'Admin::getPatientDetails');
$routes->post('admin/delete_patient', 'Admin::deletePatient');
$routes->post('admin/update_patient', 'Admin::updatePatient');

// ============================================
// ROTAS PARA MÉDICOS (ADMIN)
// ============================================
$routes->get('admin/get_doctors', 'Admin::getDoctors');
$routes->post('admin/get_doctor_details', 'Admin::getDoctorDetails');
$routes->post('admin/delete_doctor', 'Admin::deleteDoctor');
$routes->post('admin/update_doctor', 'Admin::updateDoctor');

// ============================================
// ROTAS PARA SECRETÁRIOS (ADMIN)
// ============================================
$routes->get('admin/get_secretaries', 'Admin::getSecretaries');
$routes->post('admin/get_secretary_details', 'Admin::getSecretaryDetails');
$routes->post('admin/delete_secretary', 'Admin::deleteSecretary');

// ============================================
// ROTAS PARA AGENDAMENTOS (ADMIN)
// ============================================
$routes->get('admin/get_appointments', 'Admin::getAppointments');
$routes->post('admin/get_appointment_details', 'Admin::getAppointmentDetails');
$routes->post('admin/delete_appointment', 'Admin::deleteAppointment');
$routes->post('admin/cancel_appointment', 'Admin::cancelAppointment');
$routes->post('admin/update_appointment', 'Admin::updateAppointment');

// Rotas para cadastro
$routes->post('admin/create_patient', 'Admin::createPatient');
$routes->post('admin/create_secretary', 'Admin::createSecretary');
$routes->post('admin/update_secretary', 'Admin::updateSecretary');
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

// ============================================
// ROTAS DO MÉDICO
// ============================================

// Páginas principais (views)
$routes->get('medico', 'Medico::index');
$routes->get('medico/agenda', 'Medico::agenda');
$routes->get('medico/pacientes', 'Medico::pacientes');
$routes->get('medico/disponibilidade', 'Medico::disponibilidade');
$routes->get('medico/historico', 'Medico::historico');
$routes->get('medico/perfil', 'Medico::perfil');

// Rotas AJAX do Médico
$routes->get('medico/metrics', 'Medico::metrics');
$routes->get('medico/proxima_consulta', 'Medico::proximaConsulta');
$routes->get('medico/chart_consultas_trend', 'Medico::chartConsultasTrend');
$routes->get('medico/chart_status', 'Medico::chartStatus');
$routes->get('medico/chart_novos_retorno', 'Medico::chartNovosRetorno');
$routes->get('medico/chart_faixa_etaria', 'Medico::chartFaixaEtaria');
$routes->get('medico/get_agenda', 'Medico::getAgendaData');
$routes->get('medico/proximas_consultas', 'Medico::proximasConsultas');
$routes->get('medico/get_patients', 'Medico::getPatients');
$routes->get('medico/get_history', 'Medico::getHistory');

// Ações POST do Médico
$routes->post('medico/update_appointment_status', 'Medico::updateAppointmentStatus');
$routes->post('medico/save_schedule', 'Medico::saveSchedule');
$routes->post('medico/delete_schedule', 'Medico::deleteSchedule');
$routes->post('medico/update_profile', 'Medico::updateProfile');

// Paciente
$routes->get('medico/patient/(:any)', 'Medico::patient/$1');

// Rotas para disponibilidade do médico
$routes->get('medico/get_schedules', 'Medico::getSchedules');

// Rotas para histórico
$routes->get('medico/get_history', 'Medico::getHistory');
$routes->post('medico/get_appointment_detail', 'Medico::getAppointmentDetail');

// ============================================
// ROTAS DO SECRETÁRIO
// ============================================

// Páginas principais (views)
$routes->get('secretario', 'Secretario::index');
$routes->get('secretario/agendamentos', 'Secretario::agendamentos');
$routes->get('secretario/pacientes', 'Secretario::pacientes');
$routes->get('secretario/medicos', 'Secretario::medicos');

// Rotas AJAX do Secretário
$routes->get('secretario/metrics', 'Secretario::metrics');
$routes->get('secretario/get_appointments', 'Secretario::getAppointments');
$routes->get('secretario/get_patients', 'Secretario::getPatients');
$routes->get('secretario/get_doctors', 'Secretario::getDoctors');
$routes->post('secretario/update_appointment_status', 'Secretario::updateAppointmentStatus');

$routes->get('create-secretario', 'CreateSecretarioUser::index');