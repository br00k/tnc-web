;; Copyright (c) 2011, Trans-European Research and Education Networking
;; Association (TERENA). All rights reserved.
;; 
;; Redistribution and use in source and binary forms, with or without
;; modification, are permitted provided that the following conditions are met:
;;     * Redistributions of source code must retain the above copyright
;;       notice, this list of conditions and the following disclaimer.
;;     * Redistributions in binary form must reproduce the above copyright
;;       notice, this list of conditions and the following disclaimer in the
;;       documentation and/or other materials provided with the distribution.
;;     * Neither the name of TERENA nor the
;;       names of its contributors may be used to endorse or promote products
;;       derived from this software without specific prior written permission.
;; 
;; THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
;; ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
;; WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
;; DISCLAIMED. IN NO EVENT SHALL TERENA BE LIABLE FOR ANY
;; DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
;; (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
;; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
;; ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
;; (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
;; SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
;;
;; {banner}

zend.location = "{zend_location}"

[production]
Autoloadernamespaces[] = "Zend_"
Autoloadernamespaces[] = "TA_"

phpSettings.display_startup_errors = 0
phpSettings.display_errors = 0
phpSettings.error_reporting = 8191
phpSettings.date.timezone = "Europe/Amsterdam"

bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
bootstrap.class = "Bootstrap"

appnamespace = "Application"

resources.frontController.params.prefixDefaultModule = true
resources.frontController.params.displayExceptions = 0
resources.frontController.throwerrors = false
resources.frontController.plugins.ConferenceInit = "Application_Plugin_ConferenceInit"
resources.frontController.plugins.NavigationSelector = "TA_Controller_Plugin_NavigationSelector"
resources.frontController.plugins.LayoutPicker = "TA_Controller_Plugin_LayoutPicker"
resources.frontController.plugins.LangSelector = "TA_Controller_Plugin_LangSelector"
resources.frontController.plugins.Acl = "Application_Plugin_Acl"
resources.frontController.plugins.Module = "Application_Plugin_Module"
;resources.frontController.plugins.Accept = "Application_Plugin_Accept"
resources.frontController.params.disableOutputBuffering = true

resources.log.stream.writerName = "Stream"
resources.log.stream.writerParams.stream = APPLICATION_PATH "/../logs/application.log"
resources.log.stream.writerParams.mode = "a"
resources.log.stream.filterName = "Priority"
resources.log.stream.filterParams.priority = 4

;; Mail options
resources.mail.transport.type = smtp
resources.mail.transport.host = "{mail_transport_host}"
;;resources.mail.transport.ssl = "tls"
resources.mail.transport.port = "{mail_transport_port}"

resources.mail.defaultFrom.email = "{default_from_email}"
resources.mail.defaultFrom.name = "{default_from_name}"
resources.mail.defaultReplyTo.email = "{default_replyto_email}"
resources.mail.defaultReplyTo.name = "{default_replyto_name}"
;; email address to send debug messages to
core.debugMailTo = "{debug_mailto}"

;; == Module Specific Settings ==
resources.frontController.moduleDirectory = APPLICATION_PATH "/modules"
resources.frontController.defaultModule   = "core"
resources.modules[] = ""

resources.db.adapter = "pdo_pgsql"
resources.db.params.host = "{db_hostname}"
resources.db.params.username = "{db_username}"
resources.db.params.password = "{db_password}"
resources.db.params.dbname = "{db_name}"
resources.db.isDefaultTableAdapter = true
resources.db.params.charset = "utf8"

resources.view.helperPath.Core_View_Helper = APPLICATION_PATH "/modules/core/views/helpers"
resources.layout.layoutPath = APPLICATION_PATH "/layouts/scripts"
resources.layout.layout = "core"

resources.cachemanager.simple.frontend.name = Core
resources.cachemanager.simple.frontend.options.lifetime = null
resources.cachemanager.simple.frontend.options.automatic_serialization = true
resources.cachemanager.simple.backend.name = File
resources.cachemanager.simple.backend.options.cache_dir = APPLICATION_PATH "/../cache"

resources.cachemanager.apc.frontend.name = Core
resources.cachemanager.apc.frontend.options.lifetime = null
resources.cachemanager.apc.frontend.options.automatic_serialization = true
resources.cachemanager.apc.backend.name = Apc

directories.files = APPLICATION_PATH "/../public/files/"
directories.uploads = APPLICATION_PATH "/../uploads/"

; SimpleSAMLphp
simplesaml.dir = "{ssp_location}"
simplesaml.authsource = "{ssp_authsource}"
simplesaml.saml_uid_attribute = "{ssp_uid_attribute}"
simplesaml.saml_fname_attribute = "{ssp_fname_attribute}"
simplesaml.saml_lname_attribute = "{ssp_lname_attribute}"
simplesaml.saml_organisation_attribute = "{ssp_organisation_attribute}"
simplesaml.saml_email_attribute = "{ssp_email_attribute}"
simplesaml.saml_country_attribute = "{ssp_country_attribute}"

;; email address to send debug messages to
core.debugMailTo = "gijtenbeek@terena.org"
;; observers
core.observer.review = 1
core.observer.submit = 1
core.observer.tiebreaker.notify = 1
;; log simplesaml attributes
core.logSamlAttributes = 1
;; how long before an invite expires 
core.userInviteTtl = '3 months'
;; number of seconds before session start after which user can no longer edit their presentation
core.presentation.deadline = '7200'
;; review tiebreaker value
core.review.tiebreaker = 0.13

;; Diagnostic -- uncomment the line below to enable
;diagnostic.log = APPLICATION_PATH "/../logs/diagnostic.log"
; change to "a" to append rather than overwrite diagnostic log
diagnostic.mode = "a"
; switches are in this order:
;	0 = routestartup
;	1 = routeshutdown
;	2 = dispatchloopstartup
;	3 = dispatchloopshutdown
;	4 = predispatch
;	5 = postdispatch
; set any of these to '0' to disable that diagnostic
diagnostic.switch.0 = 0
diagnostic.switch.1 = 0
diagnostic.switch.2 = 0
diagnostic.switch.3 = 1
diagnostic.switch.4 = 1
diagnostic.switch.5 = 1

[testing : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1

[development : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1
phpSettings.error_reporting = 2147483647
resources.frontController.params.displayExceptions = 1

resources.db.params.profiler.enabled = true 
resources.db.params.profiler.class	= "Zend_Db_Profiler_Firebug"

resources.log.stream.filterParams.priority = 7
resources.frontController.params.displayExceptions = 1
resources.log.firebug.writerName = "Firebug"
resources.log.firebug.filterName = "Priority"
resources.log.firebug.filterParams.priority = 7
