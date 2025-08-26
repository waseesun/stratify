"use client"
import { useFormStatus } from "react-dom"
import styles from "./Buttons.module.css"

export function LoginButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.loginButton}>
      {pending ? "Logging in..." : "Login"}
    </button>
  )
}

export function RegisterButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.registerButton}>
      {pending ? "Registering..." : "Register"}
    </button>
  )
}

export function UserTypeButton({ children, onClick, userType, disabled }) {
  return (
    <button className={styles.button} onClick={() => onClick(userType)} disabled={disabled}>
      {children}
    </button>
  )
}


export function UpdateButton({ children, type = "submit" }) {
  const { pending } = useFormStatus()

  return (
    <button type={type} className={styles.updateButton} disabled={pending}>
      {pending ? "Updating..." : children}
    </button>
  )
}




