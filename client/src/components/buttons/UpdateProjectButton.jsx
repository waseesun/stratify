"use client"

import styles from "./UpdateProjectButton.module.css"

export default function UpdateProjectButton({ onClick }) {
  return (
    <button className={styles.button} onClick={onClick}>
      Update Project
    </button>
  )
}
