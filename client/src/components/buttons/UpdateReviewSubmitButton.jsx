import { useFormStatus } from "react-dom"
import styles from "./UpdateReviewSubmitButton.module.css"

export default function UpdateReviewSubmitButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.submitButton}>
      {pending ? "Updating..." : "Update Review"}
    </button>
  )
}
