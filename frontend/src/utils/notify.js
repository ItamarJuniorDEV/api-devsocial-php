import { Notify } from 'quasar'

export function notifyError(message) {
  Notify.create({
    type: 'negative',
    message: message || 'Algo deu errado',
    position: 'top',
    timeout: 4000
  })
}

export function notifyOk(message) {
  Notify.create({
    type: 'positive',
    message: message || 'Pronto',
    position: 'top',
    timeout: 3000
  })
}
