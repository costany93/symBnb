<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="La date d'arrivé doit etre au bon format")
     * @Assert\GreaterThan("today", message="la date de reservation doit etre   supérieur à la date d'aujourd'hui", groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Assert\Date(message="La date de départ doit etre au bon format")
     * @Assert\GreaterThan(propertyPath="startDate", message="la date de départ doit etre plus éloigné que la date d'arrivé")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * CallBack appelé à chaque fois que l'on veut persisté 
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)){
            //iic on calcule le montant on fonction du prix de l'annonce et de la durée du séjour
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }


    /**
     * Cette fonction nous permet de savoir
     */
    public function isBookableDates(){
        //il faut connaitre les dates qui sont impossibles pour l'annonce 
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //Il faut comparer les dates choisies avec les dates impossibles
        $bookingsDay = $this->getDays();

        $formatDay = function($day){
            return $day->format('Y-m-d');
        };
        //Convertit les jours de réservation bookingsDay de type DateTime en des chaines de caractère que l'on peut facilement comparer
        $days = array_map($formatDay,$bookingsDay);

        //Un tableau des journées non disponible en chaine de caractère
        $notAvailable = array_map($formatDay, $notAvailableDays);

        //Ici on compare si les jours de réservation sont égales aux jours non disponibles
        foreach($days as $day){
            if(array_search($day, $notAvailable) !== false) return false;
        }

        return true;

    }

    /**
     * Permet de récupéré un tableau de journée qui correspondent à ma reservation
     * 
     * @return array un tableau d'objet datetime qui représente le jour des réservations
     */
    public function getDays(){
        $resulats = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function($daysTimestamp){
            return new DateTime(date('Y-m-d', $daysTimestamp));
        }, $resulats);

        return $days;
    }

    //Cette fonction nous permet de récupéré le nombre de jour entre la date de départ et la date d'arrivé

    public function getDuration(){
        //ici on fais la différence entre la date de départ et la date d'arrivé
        $diff = $this->endDate->diff($this->startDate);

        //Ici on récupère uniquement le nombre de jour 
        return $diff->days;
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

   
}
