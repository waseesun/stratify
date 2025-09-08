"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { updateUserPortfolioLinksAction } from "@/actions/userActions"
import { UpdateButton } from "@/components/buttons/Buttons"
import styles from "./PortfolioForm.module.css"

export default function PortfolioForm({ userId, initialLinks }) {
  const router = useRouter()
  const [links, setLinks] = useState([""])
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")

  useEffect(() => {
    if (initialLinks && initialLinks.length > 0) {
      const urls = initialLinks.map(linkObj => linkObj.link)
      setLinks([...urls, ""])
    } else {
      setLinks([""])
    }
  }, [initialLinks])

  const addLinkField = () => {
    setLinks([...links, ""])
  }

  const removeLinkField = (index) => {
    const newLinks = links.filter((_, i) => i !== index)
    setLinks(newLinks)
  }

  const handleLinkChange = (index, value) => {
    const newLinks = [...links]
    newLinks[index] = value
    setLinks(newLinks)
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccess("")

    try {
      const validLinks = links.filter((link) => link.trim() !== "")

      const result = await updateUserPortfolioLinksAction(userId, {
        links: validLinks,
      })
      console.log(result)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccess(result.success)
        setTimeout(() => {
          router.push(`/profile/${userId}`)
        }, 1500)
      }
    } catch (error) {
      console.error("An unexpected error occurred:", error)
      setErrors({ general: "An unexpected error occurred" })
    }
  }

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Update Portfolio Links</h2>

      {success && <div className={styles.success}>{success}</div>}
      {errors.general && <div className={styles.error}>{errors.general}</div>}

      <form onSubmit={handleSubmit} className={styles.form}>
        {links.map((link, index) => (
          <div key={index} className={styles.formGroup}>
            <label htmlFor={`link-${index}`} className={styles.label}>
              Link {index + 1}
            </label>
            <div className={styles.inputContainer}>
              <input
                type="text"
                id={`link-${index}`}
                name={`link-${index}`}
                className={styles.input}
                placeholder="https://example.com/portfolio"
                value={link}
                onChange={(e) => handleLinkChange(index, e.target.value)}
              />
              {links.length > 1 && (
                <button
                  type="button"
                  onClick={() => removeLinkField(index)}
                  className={styles.removeButton}
                >
                  −
                </button>
              )}
            </div>
          </div>
        ))}
        {errors.links && (
          <div className={styles.error}>{errors.links}</div>
        )}

        <button
          type="button"
          onClick={addLinkField}
          className={styles.addButton}
        >
          + Add Another Link
        </button>

        <UpdateButton>Update Portfolio Links</UpdateButton>
      </form>
    </div>
  )
}