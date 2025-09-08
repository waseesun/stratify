"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { getUserAction, updateUserAction } from "@/actions/userActions"
import { UpdateButton } from "@/components/buttons/Buttons"
import styles from "./ProfileForm.module.css"

export default function ProfileForm({ userId }) {
  const router = useRouter()
  const [formData, setFormData] = useState({
    email: "",
    username: "",
    first_name: "",
    last_name: "",
    address: "",
    description: "",
    image_url: null,
  })
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")
  const [fileName, setFileName] = useState("No file chosen")

  useEffect(() => {
    const fetchUser = async () => {
      const result = await getUserAction(userId)

      if (result.error) {
        console.error(result.error)
        return
      }

      const userData = result.data
      setFormData({
        email: userData.email || "",
        username: userData.username || "",
        first_name: userData.first_name || "",
        last_name: userData.last_name || "",
        address: userData.address || "",
        description: userData.description || "",
        image_url: null, // Reset the image field
      })
    }

    fetchUser()
  }, [userId])

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }))
  }

  const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
      setFileName(file.name)
      setFormData((prevData) => ({
        ...prevData,
        image_url: file,
      }))
    } else {
      setFileName("No file chosen")
      setFormData((prevData) => ({
        ...prevData,
        image_url: null,
      }))
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccess("")

    const formPayload = new FormData(e.target)

    if (!formPayload.get("password")) {
      formPayload.delete("password")
      formPayload.delete("password_confirmation")
    }

    try {
      const result = await updateUserAction(userId, formPayload)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccess(result.success)
      }
    } catch (error) {
      setErrors({ general: "An unexpected error occurred" })
    }
  }

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Update Profile</h2>

      {success && <div className={styles.success}>{success}</div>}
      {errors.error && <div className={styles.error}>{errors.error}</div>}

      <form onSubmit={handleSubmit} className={styles.form}>
        <div className={styles.formGroup}>
          <label htmlFor="email" className={styles.label}>
            Email
          </label>
          <input
            type="email"
            id="email"
            name="email"
            className={styles.input}
            required
            value={formData.email}
            onChange={handleInputChange}
          />
          {errors.email && <span className={styles.fieldError}>{errors.email}</span>}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="username" className={styles.label}>
            Username
          </label>
          <input
            type="text"
            id="username"
            name="username"
            className={styles.input}
            value={formData.username}
            onChange={handleInputChange}
          />
          {errors.username && <span className={styles.fieldError}>{errors.username}</span>}
        </div>

        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label htmlFor="first_name" className={styles.label}>
              First Name
            </label>
            <input
              type="text"
              id="first_name"
              name="first_name"
              className={styles.input}
              value={formData.first_name}
              onChange={handleInputChange}
            />
            {errors.first_name && <span className={styles.fieldError}>{errors.first_name}</span>}
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="last_name" className={styles.label}>
              Last Name
            </label>
            <input
              type="text"
              id="last_name"
              name="last_name"
              className={styles.input}
              value={formData.last_name}
              onChange={handleInputChange}
            />
            {errors.last_name && <span className={styles.fieldError}>{errors.last_name}</span>}
          </div>
        </div>

        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label htmlFor="password" className={styles.label}>
              Password
            </label>
            <input
              type="password"
              id="password"
              name="password"
              className={styles.input}
            />
            {errors.password && <span className={styles.fieldError}>{errors.password}</span>}
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="password_confirmation" className={styles.label}>
              Confirm Password
            </label>
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              className={styles.input}
            />
          </div>
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="address" className={styles.label}>
            Address
          </label>
          <input
            type="text"
            id="address"
            name="address"
            className={styles.input}
            value={formData.address}
            onChange={handleInputChange}
          />
          {errors.address && <span className={styles.fieldError}>{errors.address}</span>}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="description" className={styles.label}>
            Description
          </label>
          <textarea
            id="description"
            name="description"
            className={styles.textarea}
            rows="4"
            value={formData.description}
            onChange={handleInputChange}
          />
          {errors.description && <span className={styles.fieldError}>{errors.description}</span>}
        </div>

        <div className={styles.formGroup}>
          <div className={styles.Fileinputgroup}>
            <label htmlFor="image" className={styles.Filelabel}>
              File Image
            </label>
            <input
              type="file"
              id="image"
              name="image_url"
              accept="image/*"
              onChange={handleFileChange}
              className={styles.Filehidden}
            />
            <div className={styles.Filefileupload}>
              <label htmlFor="image" className={styles.Filebutton}>
                Choose File
              </label>
              <span id="fileNameDisplay" className={styles.Filefilename}>
                {fileName}
              </span>
            </div>
          </div>
          {errors.image && <span className={styles.errorText}>{errors.image}</span>}
        </div>

        <UpdateButton>Update Profile</UpdateButton>
      </form>
    </div>
  )
}