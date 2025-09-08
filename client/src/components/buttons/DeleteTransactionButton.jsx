"use client"

import styles from "./DeleteTransactionButton.module.css"

export default function DeleteTransactionButton({ onClick }) {
  return (
    <button type="button" className={styles.button} onClick={onClick}>
      Delete Transaction
    </button>
  )
}
