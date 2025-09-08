"use client"

import styles from "./CreateTransactionButton.module.css"

export default function CreateTransactionButton({ onClick }) {
  return (
    <button type="button" className={styles.button} onClick={onClick}>
      Create Transaction
    </button>
  )
}
