"use client"

import styles from "./DeleteNotificationButton.module.css"

export default function DeleteNotificationButton({ onClick }) {
  return (
    <button className={styles.button} onClick={onClick} type="button">
      Delete
    </button>
  )
}
