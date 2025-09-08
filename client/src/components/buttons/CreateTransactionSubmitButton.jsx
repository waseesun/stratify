"use client"

import { useFormStatus } from "react-dom"
import styles from "./CreateTransactionSubmitButton.module.css"

export default function CreateTransactionSubmitButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" className={styles.button} disabled={pending}>
      {pending ? "Creating..." : "Create Transaction"}
    </button>
  )
}
