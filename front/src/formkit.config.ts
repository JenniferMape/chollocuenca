// Importar las configuraciones predeterminadas y el idioma español
import { es } from '@formkit/i18n'
import { defaultConfig } from '@formkit/vue'
import { rootClasses } from '@/formkit.theme'

// Crear la configuración personalizada para FormKit
export default defaultConfig({
  locales: { es },     
  locale: 'es',        
  validationMessages: {
    required: 'Este campo es obligatorio',
    email: 'Por favor, ingresa un correo electrónico válido',
    minLength: 'Este campo debe tener al menos {min} caracteres',
    dateAfterNode:'La fecha de fin no puede ser anterior a la de inicio'

  },
  config: {
    rootClasses
  }
})
