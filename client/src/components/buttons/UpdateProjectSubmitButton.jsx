import styles from "./UpdateProjectSubmitButton.module.css"

export default function UpdateProjectSubmitButton({ pending, text }) {
  return (
    <button type="submit" className={styles.button} disabled={pending}>
      {pending ? "Processing..." : text}
    </button>
  )
}
